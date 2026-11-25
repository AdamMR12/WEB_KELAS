(function(){
'use strict';
var BASE_HTML = '<!doctype html>\n' + document.documentElement.outerHTML;
var DRAFT_KEY = 'kas-si2a-draft';
var AUTH_KEY = 'kas-si2a-auth';

function $(s, r){ return (r || document).querySelector(s); }
function h(tag, attrs){
  var el = document.createElement(tag);
  if (attrs) for (var k in attrs){
    var v = attrs[k];
    if (v == null || v === false) continue;
    if (k === 'class') el.className = v;
    else if (k === 'text') el.textContent = v;
    else if (k.indexOf('on') === 0) el.addEventListener(k.slice(2), v);
    else if (v === true) el.setAttribute(k, '');
    else el.setAttribute(k, v);
  }
  for (var i = 2; i < arguments.length; i++){
    var kids = [].concat(arguments[i]);
    for (var j = 0; j < kids.length; j++){
      var c = kids[j];
      if (c == null || c === false) continue;
      el.append(c.nodeType ? c : document.createTextNode(String(c)));
    }
  }
  return el;
}

/* ---------- state ---------- */
var state;
try { state = JSON.parse($('#kas-data').textContent); }
catch (e) { state = {updatedAt:new Date().toISOString(),settings:{iuran:20000,bulan:1,demo:false,pinHash:''},admins:[],mahasiswa:[],transaksi:[]}; }
try {
  var draft = JSON.parse(localStorage.getItem(DRAFT_KEY) || 'null');
  if (draft && draft.updatedAt > state.updatedAt) state = draft;
} catch (e) {}

try {
  var baseData = JSON.parse($('#kas-data').textContent);
  state.settings = state.settings || {};
  if (!state.settings.treasurerNim) state.settings.treasurerNim = baseData.settings.treasurerNim;
  if (!state.settings.pinHash || state.settings.pinHash.indexOf('GANTI') === 0) state.settings.pinHash = baseData.settings.pinHash;
} catch (e) {}
if (!Array.isArray(state.bukti)) state.bukti = [];

var dirty = false, admin = null, mode = 'public', adminTab = 'transaksi';
var editTrx = null, editMhs = null, filterJenis = 'semua', artifactNS = null, firstPaint = true;
var reduceMotion = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
var currentRole = null, currentNim = null;

function findAuthed(){
  try {
    var saved = JSON.parse(sessionStorage.getItem(AUTH_KEY) || 'null');
    if (!saved || !saved.nim || !saved.role) return null;
    if (saved.role === 'admin' && saved.nim === state.settings.treasurerNim) return saved;
    if (saved.role === 'user' && state.mahasiswa.some(function(x){ return x.nim === saved.nim; })) return saved;
  } catch (e) {}
  return null;
}

/* ---------- helpers ---------- */
function rp(n){ return (n < 0 ? '-' : '') + 'Rp\u00a0' + Math.abs(Math.round(n)).toLocaleString('id-ID'); }
function fmtDateTime(iso){
  try { return new Intl.DateTimeFormat('id-ID',{dateStyle:'long',timeStyle:'short',timeZone:'Asia/Jakarta'}).format(new Date(iso)).replace(/\./g,'.') + ' WIB'; }
  catch (e) { return iso; }
}
function fmtDate(tgl){
  try { return new Intl.DateTimeFormat('id-ID',{day:'numeric',month:'short',year:'numeric'}).format(new Date(tgl + 'T00:00:00')); }
  catch (e) { return tgl; }
}
function todayStr(){
  try { return new Date().toLocaleDateString('sv-SE',{timeZone:'Asia/Jakarta'}); } catch (e) { return new Date().toISOString().slice(0,10); }
}
function seq(id){ return parseInt(String(id).slice(1), 10) || 0; }
function nextId(prefix, list){ var m = 0; list.forEach(function(x){ m = Math.max(m, seq(x.id)); }); return prefix + (m + 1); }
function nameOf(id){ var m = state.mahasiswa.filter(function(x){ return x.id === id; })[0]; return m ? m.nama : ''; }
function initials(n){ var p = n.trim().split(/\s+/); return ((p[0] || '')[0] || '?').toUpperCase() + ((p[1] || '')[0] || '').toUpperCase(); }
var AV = ['#FFC83D','#8BE8C2','#FFB3C1','#9CD3FF','#C9BDFF'];
function avColor(s){ var x = 0; for (var i = 0; i < s.length; i++) x = (x * 31 + s.charCodeAt(i)) >>> 0; return AV[x % AV.length]; }
async function sha(t){
  var b = await crypto.subtle.digest('SHA-256', new TextEncoder().encode(t));
  return Array.from(new Uint8Array(b)).map(function(x){ return x.toString(16).padStart(2,'0'); }).join('');
}

function totals(){
  var masuk = 0, keluar = 0;
  state.transaksi.forEach(function(t){ if (t.jenis === 'masuk') masuk += t.nominal; else keluar += t.nominal; });
  return {masuk:masuk, keluar:keluar, saldo:masuk - keluar};
}
function ledger(){
  var s = state.transaksi.slice().sort(function(a,b){ return a.tgl.localeCompare(b.tgl) || seq(a.id) - seq(b.id); });
  var run = 0;
  s.forEach(function(t){ run += t.jenis === 'masuk' ? t.nominal : -t.nominal; t._saldo = run; });
  return s.reverse();
}
function status(m){
  var paid = 0;
  state.transaksi.forEach(function(t){ if (t.jenis === 'masuk' && t.mhs === m.id) paid += t.nominal; });
  var target = state.settings.iuran * state.settings.bulan;
  var tunggak = Math.max(0, target - paid);
  return {paid:paid, target:target, tunggak:tunggak, lunas:tunggak === 0};
}

/* ---------- toast + confirm ---------- */
var toastTimer;
function toast(msg){
  var t = $('#toast'); t.textContent = msg; t.classList.add('show');
  clearTimeout(toastTimer); toastTimer = setTimeout(function(){ t.classList.remove('show'); }, 4200);
}
function confirmBox(msg, yesLabel){
  return new Promise(function(res){
    var d = $('#dlgConfirm'); $('#dlgConfirmMsg').textContent = msg; $('#cfYes').textContent = yesLabel || 'Ya, hapus';
    d.returnValue = '';
    d.addEventListener('close', function once(){ d.removeEventListener('close', once); res(d.returnValue === 'ya'); });
    d.showModal();
  });
}

/* ---------- public view ---------- */
function setSaldo(v){
  var el = $('#saldoNum'), fin = rp(v);
  $('#saldoSr').textContent = fin;
  if (firstPaint && !reduceMotion && v > 0){
    var t0 = performance.now(), dur = 900;
    (function tick(now){
      var p = Math.min(1, (now - t0) / dur), e = 1 - Math.pow(1 - p, 3);
      el.textContent = rp(v * e);
      if (p < 1) requestAnimationFrame(tick); else el.textContent = fin;
    })(t0);
  } else el.textContent = fin;
  firstPaint = false;
}
function renderSummary(){
  var t = totals();
  setSaldo(t.saldo);
  $('#totMasuk').textContent = rp(t.masuk);
  $('#totKeluar').textContent = rp(t.keluar);
  var when = fmtDateTime(state.updatedAt);
  $('#updTime').textContent = when; $('#updTime2').textContent = when;
  $('#demoBanner').hidden = !state.settings.demo;
  $('#teamLine').textContent = 'Dikelola Kelompok 6: ' + state.admins.map(function(a){ return a.nama; }).join(', ') + '.';
}
function renderIuran(){
  var q = ($('#q').value || '').trim().toLowerCase();
  var all = state.mahasiswa.map(function(m){ return {m:m, s:status(m)}; });
  var lunas = all.filter(function(x){ return x.s.lunas; }).length;
  $('#sum').textContent = all.length ? lunas + ' dari ' + all.length + ' mahasiswa sudah lunas' : '';
  $('#barFill').style.width = all.length ? Math.round(lunas / all.length * 100) + '%' : '0%';
  var list = all.filter(function(x){ return !q || x.m.nama.toLowerCase().indexOf(q) > -1 || x.m.nim.toLowerCase().indexOf(q) > -1; });
  var ul = $('#res'); ul.replaceChildren();
  if (!all.length){ ul.append(h('li',{class:'none'}, h('strong',{text:'Daftar mahasiswa belum diisi.'}), 'Admin bisa menambahkannya di area admin.')); return; }
  if (!list.length){ ul.append(h('li',{class:'none'}, h('strong',{text:'Data tidak ditemukan.'}), 'Coba ketik nama depan atau NIM lengkap.')); return; }
  list.forEach(function(x){
    var badge = x.s.lunas
      ? h('span',{class:'badge ok'}, h('span',{text:'\u2713 Lunas'}))
      : h('span',{class:'badge no'}, h('span',{text:'Belum lunas'}), h('small',{text:'Tunggakan ' + rp(x.s.tunggak)}));
    ul.append(h('li',{class:'r'},
      h('span',{class:'av','aria-hidden':'true',style:'background:' + avColor(x.m.nim)}, initials(x.m.nama)),
      h('div',{class:'rn'}, h('strong',{text:x.m.nama}), h('span',{class:'nim',text:x.m.nim})),
      badge));
  });
}
function renderRiwayat(){
  var tb = $('#rows'); tb.replaceChildren();
  var rows = ledger().filter(function(t){ return filterJenis === 'semua' || t.jenis === filterJenis; });
  $('#emptyHist').hidden = rows.length > 0;
  document.querySelector('.tbl').hidden = rows.length === 0;
  rows.forEach(function(t){
    var masuk = t.jenis === 'masuk';
    var who = t.mhs ? nameOf(t.mhs) : '';
    var link = null;
    if (isImg(t.foto)){
      link = h('button',{class:'thumb',type:'button','aria-label':'Lihat foto bukti pembayaran ' + t.ket,onclick:function(){ openPhoto(t.foto, t.ket + ' - ' + fmtDate(t.tgl)); }},
        h('img',{src:t.foto,alt:'Bukti pembayaran: ' + t.ket,loading:'lazy'}), h('span',{text:'Lihat bukti'}));
    } else if (/^https?:\/\//i.test(t.bukti || '')){
      link = h('a',{class:'sub',href:t.bukti,target:'_blank',rel:'noopener noreferrer',text:'Lihat bukti'});
    }
    tb.append(h('tr', null,
      h('td',{class:'tgl',text:fmtDate(t.tgl)}),
      h('td',{class:'jns'}, h('span',{class:'badge tag ' + (masuk ? 'ok' : 'no'),text:masuk ? 'Pemasukan' : 'Pengeluaran'})),
      h('td',{class:'ket'}, h('span',{text:t.ket}), who ? h('span',{class:'sub',text:'Dari ' + who}) : null, link),
      h('td',{class:'num nom ' + (masuk ? 'plus' : 'minus'),text:(masuk ? '+' : '-') + rp(t.nominal)}),
      h('td',{class:'num sal',text:rp(t._saldo)})));
  });
}

function photoPicker(){
  var data = '';
  var input = h('input',{id:'b-foto',type:'file',accept:'image/*',hidden:true,tabindex:'-1'});
  var prev = h('div',{class:'fprev'});
  function show(){
    prev.replaceChildren();
    if (!data) return;
    prev.append(h('img',{src:data,alt:'Pratinjau foto bukti pembayaran'}),
      h('button',{class:'btn ghost',type:'button',text:'Hapus foto',onclick:function(){ data = ''; input.value = ''; show(); }}));
  }
  async function handle(f){
    if (!f) return;
    if (!/^image\//.test(f.type)){ toast('File harus berupa gambar (JPG, PNG, dll).'); return; }
    if (f.size > 15 * 1024 * 1024){ toast('Ukuran foto terlalu besar. Maksimal 15 MB.'); return; }
    try { data = await compressImage(f, 900, 0.72); show(); toast('Foto siap diunggah.'); }
    catch (e){ input.value = ''; toast(e.message || 'Foto tidak bisa dipakai.'); }
  }
  input.addEventListener('change', function(){ handle(input.files && input.files[0]); });
  var btn = h('button',{class:'btn',type:'button',text:'Pilih foto dari komputer / galeri',onclick:function(){ input.click(); }});
  var drop = h('div',{class:'drop',role:'group','aria-label':'Area unggah foto bukti pembayaran'},
    h('p',{class:'drop-t',text:'Seret dan lepas foto ke sini'}),
    h('p',{class:'hint',text:'atau'}), btn, input);
  ['dragenter','dragover'].forEach(function(ev){ drop.addEventListener(ev, function(e){ e.preventDefault(); drop.classList.add('over'); }); });
  ['dragleave','dragend'].forEach(function(ev){ drop.addEventListener(ev, function(){ drop.classList.remove('over'); }); });
  drop.addEventListener('drop', function(e){
    e.preventDefault(); drop.classList.remove('over');
    handle(e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0]);
  });
  return {el:h('div',null,drop,prev), get:function(){ return data; }};
}
function buktiSorted(){
  return state.bukti.slice().sort(function(a, b){ return a.tgl < b.tgl ? 1 : a.tgl > b.tgl ? -1 : (a.id < b.id ? 1 : -1); });
}
function buktiCap(b){
  var who = b.mhs ? nameOf(b.mhs) : '';
  return b.ket + (who ? ' - ' + who : '') + ' - ' + fmtDate(b.tgl);
}
function renderBukti(){
  var g = $('#buktiGrid'); if (!g) return;
  g.replaceChildren();
  var list = buktiSorted();
  $('#emptyBukti').hidden = list.length > 0;
  list.forEach(function(b){
    var who = b.mhs ? nameOf(b.mhs) : '';
    g.append(h('button',{class:'bcard',type:'button','aria-label':'Lihat foto bukti: ' + b.ket,onclick:function(){ openPhoto(b.foto, buktiCap(b)); }},
      h('img',{src:b.foto,alt:'Bukti pembayaran: ' + b.ket,loading:'lazy'}),
      h('strong',{text:b.ket}),
      h('span',{class:'sub',text:(who ? who + ' - ' : '') + fmtDate(b.tgl)}),
      b.nominal ? h('span',{class:'sub',text:rp(b.nominal)}) : null));
  });
}
function renderPublic(){ renderSummary(); renderIuran(); renderRiwayat(); renderBukti(); }


/* ---------- foto bukti pembayaran ---------- */
function compressImage(file, maxSide, quality){
  return new Promise(function(resolve, reject){
    if (!file || !/^image\//.test(file.type)){ reject(new Error('Bukan file gambar.')); return; }
    var fr = new FileReader();
    fr.onerror = function(){ reject(new Error('Gagal membaca file.')); };
    fr.onload = function(){
      var img = new Image();
      img.onerror = function(){ reject(new Error('Gambar tidak bisa dibuka.')); };
      img.onload = function(){
        var w = img.width, hgt = img.height, k = Math.min(1, maxSide / Math.max(w, hgt));
        var c = document.createElement('canvas');
        c.width = Math.round(w * k); c.height = Math.round(hgt * k);
        var ctx = c.getContext('2d');
        ctx.fillStyle = '#fff'; ctx.fillRect(0, 0, c.width, c.height);
        ctx.drawImage(img, 0, 0, c.width, c.height);
        resolve(c.toDataURL('image/jpeg', quality));
      };
      img.src = fr.result;
    };
    fr.readAsDataURL(file);
  });
}
function isImg(v){ return /^data:image\//.test(v || ''); }
function openPhoto(src, caption){
  var d = $('#dlgPhoto'); if (!d) return;
  $('#photoImg').src = src;
  $('#photoCap').textContent = caption || '';
  if (d.showModal) d.showModal(); else d.setAttribute('open','');
}

/* ---------- admin ---------- */
function field(id, label, input, hint){
  return h('div',{class:'field'}, h('label',{for:id,text:label}), input, hint ? h('small',{class:'hint',text:hint}) : null);
}
function touch(){
  state.updatedAt = new Date().toISOString();
  dirty = true; updateSaveBar(); renderPublic();
}
function updateSaveBar(){
  var show = dirty && mode === 'admin';
  $('#saveBar').hidden = !show;
  document.body.classList.toggle('has-bar', show);
  $('#saveMsg').textContent = artifactNS ? 'Ada perubahan yang belum disimpan ke website.' : 'Ada perubahan yang belum disimpan.';
  $('#saveBtn').textContent = artifactNS ? 'Simpan perubahan' : 'Simpan di perangkat ini';
}

function trxForm(){
  var t = editTrx ? state.transaksi.filter(function(x){ return x.id === editTrx; })[0] : null;
  var tgl = h('input',{class:'in',id:'f-tgl',type:'date',required:true,value:t ? t.tgl : todayStr()});
  var jenis = h('select',{class:'in',id:'f-jenis'},
    h('option',{value:'masuk',selected:!t || t.jenis === 'masuk',text:'Pemasukan'}),
    h('option',{value:'keluar',selected:t && t.jenis === 'keluar',text:'Pengeluaran'}));
  var mhs = h('select',{class:'in',id:'f-mhs'}, h('option',{value:'',text:'Bukan iuran mahasiswa'}),
    state.mahasiswa.map(function(m){ return h('option',{value:m.id,selected:t && t.mhs === m.id,text:m.nama + ' (' + m.nim + ')'}); }));
  var ket = h('input',{class:'in',id:'f-ket',type:'text',required:true,maxlength:'120',value:t ? t.ket : '',placeholder:'Contoh: Iuran September'});
  var nom = h('input',{class:'in',id:'f-nom',type:'number',required:true,min:'1',step:'1',inputmode:'numeric',value:t ? t.nominal : ''});
  var bukti = h('input',{class:'in',id:'f-bukti',type:'url',value:t ? (t.bukti || '') : '',placeholder:'https://...'});
  var fotoData = t && isImg(t.foto) ? t.foto : '';
  function sync(){ mhs.disabled = jenis.value !== 'masuk'; if (mhs.disabled) mhs.value = ''; }
  jenis.addEventListener('change', sync); sync();
  var form = h('form',{class:'card',novalidate:false},
    h('h2',{text:t ? 'Ubah transaksi' : 'Tambah transaksi'}),
    h('div',{class:'grid2'},
      field('f-tgl','Tanggal',tgl), field('f-jenis','Jenis',jenis),
      field('f-mhs','Nama mahasiswa (untuk iuran)',mhs), field('f-nom','Nominal (Rp)',nom),
      h('div',{class:'field span2'}, h('label',{for:'f-ket',text:'Keterangan'}), ket),
      h('div',{class:'field span2'}, h('label',{for:'f-bukti',text:'Tautan bukti pembayaran (opsional)'}), bukti)),
    h('div',{class:'actions'},
      h('button',{class:'btn',type:'submit',text:t ? 'Ubah transaksi' : 'Tambah transaksi'}),
      t ? h('button',{class:'btn ghost',type:'button',text:'Batal',onclick:function(){ editTrx = null; renderAdmin(); }}) : null));
  form.addEventListener('submit', function(e){
    e.preventDefault();
    var n = parseInt(nom.value, 10);
    if (!(n > 0)){ toast('Nominal harus lebih dari 0.'); return; }
    var rec = {id:t ? t.id : nextId('t', state.transaksi), tgl:tgl.value, jenis:jenis.value, mhs:jenis.value === 'masuk' ? (mhs.value || null) : null, ket:ket.value.trim(), nominal:n, bukti:bukti.value.trim(), foto:fotoData};
    if (t) Object.assign(t, rec); else state.transaksi.push(rec);
    var wasEdit = !!t; editTrx = null; touch(); renderAdmin();
    toast(wasEdit ? 'Transaksi diubah. Saldo dan status iuran sudah diperbarui.' : 'Transaksi ditambahkan. Saldo dan status iuran sudah diperbarui.');
  });
  return form;
}
function buktiForm(){
  var pick = photoPicker();
  var tgl = h('input',{class:'in',id:'b-tgl',type:'date',required:true,value:todayStr()});
  var mhs = h('select',{class:'in',id:'b-mhs'}, h('option',{value:'',text:'Tidak dipilih / pembayaran umum'}),
    state.mahasiswa.map(function(m){ return h('option',{value:m.id,text:m.nama + ' (' + m.nim + ')'}); }));
  var ket = h('input',{class:'in',id:'b-ket',type:'text',required:true,maxlength:'120',placeholder:'Contoh: Transfer iuran September'});
  var nom = h('input',{class:'in',id:'b-nom',type:'number',min:'1',step:'1',inputmode:'numeric',placeholder:'Opsional'});
  var form = h('form',{class:'card'},
    h('h2',{text:'Upload foto bukti pembayaran'}),
    h('p',{class:'hint',style:'margin-bottom:14px',text:'Foto yang diunggah di sini akan tampil di halaman Riwayat kas dan bisa dilihat semua anggota.'}),
    h('div',{class:'grid2'},
      field('b-tgl','Tanggal',tgl), field('b-mhs','Nama mahasiswa (opsional)',mhs),
      h('div',{class:'field span2'}, h('label',{for:'b-ket',text:'Keterangan'}), ket),
      field('b-nom','Nominal (Rp, opsional)',nom),
      h('div',{class:'field span2'}, h('label',{for:'b-foto',text:'Foto bukti pembayaran'}), pick.el)),
    h('div',{class:'actions'}, h('button',{class:'btn',type:'submit',text:'Upload bukti pembayaran'})));
  form.addEventListener('submit', function(e){
    e.preventDefault();
    if (!pick.get()){ toast('Pilih atau seret foto bukti pembayaran dulu.'); return; }
    var n = parseInt(nom.value, 10);
    state.bukti.push({id:nextId('b', state.bukti), tgl:tgl.value, mhs:mhs.value || null, ket:ket.value.trim(), nominal:n > 0 ? n : 0, foto:pick.get()});
    touch(); renderAdmin();
    toast('Foto bukti pembayaran diunggah. Klik Simpan perubahan agar tampil untuk semua anggota.');
  });
  return form;
}
function buktiList(){
  var box = h('div',{class:'card'}, h('h2',{text:'Foto bukti yang sudah diunggah'}));
  var list = buktiSorted();
  if (!list.length){ box.append(h('p',{class:'hint',text:'Belum ada foto bukti pembayaran.'})); return box; }
  var ul = h('ul',{class:'list'});
  list.forEach(function(b){
    var who = b.mhs ? nameOf(b.mhs) : '';
    ul.append(h('li',{class:'arow'},
      h('img',{class:'bthumb',src:b.foto,alt:'Bukti pembayaran: ' + b.ket}),
      h('div',{class:'info'}, h('strong',{text:b.ket}), h('div',{class:'hint',text:fmtDate(b.tgl) + (who ? ', ' + who : '') + (b.nominal ? ', ' + rp(b.nominal) : '')})),
      h('div',{class:'btns'},
        h('button',{class:'btn ghost',type:'button',text:'Lihat',onclick:function(){ openPhoto(b.foto, buktiCap(b)); }}),
        h('button',{class:'btn danger',type:'button',text:'Hapus','aria-label':'Hapus foto bukti ' + b.ket,onclick:async function(){
          if (await confirmBox('Hapus foto bukti pembayaran \"' + b.ket + '\"?')){
            state.bukti = state.bukti.filter(function(x){ return x.id !== b.id; });
            touch(); renderAdmin(); toast('Foto bukti dihapus.');
          }
        }}))));
  });
  box.append(ul); return box;
}

function trxList(){
  var rows = ledger();
  var box = h('div',{class:'card'}, h('h2',{text:'Semua transaksi'}));
  if (!rows.length){ box.append(h('p',{class:'hint',text:'Belum ada transaksi. Tambahkan yang pertama lewat formulir di atas.'})); return box; }
  var ul = h('ul',{class:'list'});
  rows.forEach(function(t){
    var masuk = t.jenis === 'masuk', who = t.mhs ? nameOf(t.mhs) : '';
    ul.append(h('li',{class:'arow'},
      h('div',{class:'info'},
        h('strong',{text:t.ket}),
        h('div',{class:'hint'}, fmtDate(t.tgl) + (who ? ', ' + who : '') + ', ' + (masuk ? '+' : '-') + rp(t.nominal), ' ', h('span',{class:'pill ' + t.jenis,text:masuk ? 'Pemasukan' : 'Pengeluaran'}))),
      h('div',{class:'btns'},
        h('button',{class:'btn ghost',type:'button','aria-label':'Ubah transaksi ' + t.ket,text:'Ubah',onclick:function(){ editTrx = t.id; renderAdmin(); window.scrollTo({top:0}); }}),
        h('button',{class:'btn danger',type:'button','aria-label':'Hapus transaksi ' + t.ket,text:'Hapus',onclick:async function(){
          if (await confirmBox('Hapus transaksi "' + t.ket + '" sebesar ' + rp(t.nominal) + '? Saldo kas akan dihitung ulang.')){
            state.transaksi = state.transaksi.filter(function(x){ return x.id !== t.id; });
            if (editTrx === t.id) editTrx = null;
            touch(); renderAdmin(); toast('Transaksi dihapus.');
          }
        }}))));
  });
  box.append(ul); return box;
}

function parseRoster(text){
  var out = [];
  text.split(/\r?\n/).forEach(function(line){
    var p = line.split('\t').map(function(s){ return s.trim(); }).filter(Boolean);
    if (p.length < 2) p = line.split(/[;,]/).map(function(s){ return s.trim(); }).filter(Boolean);
    var nim = p.filter(function(x){ return /^\d{5,}$/.test(x); })[0];
    if (!nim) return;
    var nama = p.filter(function(x){ return x !== nim && !/^\d{1,3}$/.test(x); }).join(' ').trim();
    if (nama) out.push({nim:nim, nama:nama});
  });
  return out;
}

function iuranTab(){
  var wrap = h('div',{class:'stack'});
  /* pengaturan */
  var nomI = h('input',{class:'in',id:'s-iuran',type:'number',min:'0',step:'1',inputmode:'numeric',value:state.settings.iuran});
  var blnI = h('input',{class:'in',id:'s-bulan',type:'number',min:'1',step:'1',inputmode:'numeric',value:state.settings.bulan});
  var fSet = h('form',{class:'card'}, h('h2',{text:'Pengaturan iuran'}),
    h('div',{class:'grid2'},
      field('s-iuran','Iuran per bulan (Rp)',nomI),
      field('s-bulan','Jumlah bulan yang ditagih',blnI,'Tunggakan dihitung dari iuran per bulan dikali jumlah bulan, dikurangi total iuran yang sudah dibayar.')),
    h('div',{class:'actions'}, h('button',{class:'btn',type:'submit',text:'Simpan pengaturan'})));
  fSet.addEventListener('submit', function(e){
    e.preventDefault();
    var a = parseInt(nomI.value,10), b = parseInt(blnI.value,10);
    if (!(a >= 0) || !(b >= 1)){ toast('Isi nominal dan jumlah bulan dengan benar.'); return; }
    state.settings.iuran = a; state.settings.bulan = b; touch(); renderAdmin(); toast('Pengaturan iuran disimpan. Status iuran diperbarui.');
  });
  wrap.append(fSet);

  /* daftar mahasiswa */
  var m = editMhs ? state.mahasiswa.filter(function(x){ return x.id === editMhs; })[0] : null;
  var nimI = h('input',{class:'in',id:'m-nim',type:'text',required:true,inputmode:'numeric',value:m ? m.nim : ''});
  var namaI = h('input',{class:'in',id:'m-nama',type:'text',required:true,value:m ? m.nama : ''});
  var fM = h('form',{class:'card'}, h('h2',{text:m ? 'Ubah mahasiswa' : 'Tambah mahasiswa'}),
    h('div',{class:'grid2'}, field('m-nim','NIM',nimI), field('m-nama','Nama lengkap',namaI)),
    h('div',{class:'actions'}, h('button',{class:'btn',type:'submit',text:m ? 'Ubah mahasiswa' : 'Tambah mahasiswa'}),
      m ? h('button',{class:'btn ghost',type:'button',text:'Batal',onclick:function(){ editMhs = null; renderAdmin(); }}) : null));
  fM.addEventListener('submit', function(e){
    e.preventDefault();
    var nim = nimI.value.trim(), nama = namaI.value.trim();
    if (state.mahasiswa.some(function(x){ return x.nim === nim && (!m || x.id !== m.id); })){ toast('NIM ' + nim + ' sudah ada di daftar.'); return; }
    if (m){ m.nim = nim; m.nama = nama; } else state.mahasiswa.push({id:nextId('m', state.mahasiswa), nim:nim, nama:nama});
    var was = !!m; editMhs = null; touch(); renderAdmin(); toast(was ? 'Data mahasiswa diubah.' : 'Mahasiswa ditambahkan.');
  });
  wrap.append(fM);

  var listCard = h('div',{class:'card'}, h('h2',{text:'Status iuran (' + state.mahasiswa.length + ' mahasiswa)'}));
  var ul = h('ul',{class:'list'});
  state.mahasiswa.forEach(function(x){
    var s = status(x);
    ul.append(h('li',{class:'arow'},
      h('div',{class:'info'}, h('strong',{text:x.nama}), h('div',{class:'hint',text:x.nim + ', sudah bayar ' + rp(s.paid) + (s.lunas ? '' : ', tunggakan ' + rp(s.tunggak))})),
      h('div',{class:'btns'},
        h('span',{class:'badge tag ' + (s.lunas ? 'ok' : 'no'),text:s.lunas ? '\u2713 Lunas' : 'Belum lunas'}),
        h('button',{class:'btn ghost',type:'button','aria-label':'Ubah ' + x.nama,text:'Ubah',onclick:function(){ editMhs = x.id; renderAdmin(); window.scrollTo({top:0}); }}),
        h('button',{class:'btn danger',type:'button','aria-label':'Hapus ' + x.nama,text:'Hapus',onclick:async function(){
          if (await confirmBox('Hapus ' + x.nama + ' dari daftar? Transaksi iurannya tetap tercatat, tetapi tidak lagi terhubung ke nama ini.')){
            state.mahasiswa = state.mahasiswa.filter(function(y){ return y.id !== x.id; });
            state.transaksi.forEach(function(t){ if (t.mhs === x.id) t.mhs = null; });
            touch(); renderAdmin(); toast('Mahasiswa dihapus dari daftar.');
          }
        }}))));
  });
  if (!state.mahasiswa.length) listCard.append(h('p',{class:'hint',text:'Daftar masih kosong. Tambahkan lewat formulir atau impor dari Google Sheets.'}));
  listCard.append(ul); wrap.append(listCard);

  /* impor */
  var ta = h('textarea',{class:'in',id:'imp',placeholder:'No\tNIM\tNama Lengkap\n1\t10112058\tNama Mahasiswa'});
  var fI = h('form',{class:'card'}, h('h2',{text:'Impor dari Google Sheets'}),
    field('imp','Tempel kolom No, NIM, dan Nama Lengkap',ta,'Salin baris dari Google Sheets lalu tempel di sini. NIM yang sudah ada akan diperbarui namanya, NIM baru akan ditambahkan.'),
    h('div',{class:'actions'}, h('button',{class:'btn coin',type:'submit',text:'Impor daftar mahasiswa'})));
  fI.addEventListener('submit', function(e){
    e.preventDefault();
    var rows = parseRoster(ta.value);
    if (!rows.length){ toast('Tidak ada baris yang terbaca. Pastikan ada kolom NIM dan nama.'); return; }
    var added = 0, updated = 0;
    rows.forEach(function(r){
      var ex = state.mahasiswa.filter(function(x){ return x.nim === r.nim; })[0];
      if (ex){ ex.nama = r.nama; updated++; } else { state.mahasiswa.push({id:nextId('m', state.mahasiswa), nim:r.nim, nama:r.nama}); added++; }
    });
    touch(); renderAdmin(); toast(added + ' mahasiswa ditambahkan, ' + updated + ' diperbarui.');
  });
  wrap.append(fI);

  /* pin */
  var pinI = h('input',{class:'in',id:'pin-baru',type:'password',autocomplete:'new-password',minlength:'6'});
  var fP = h('form',{class:'card'}, h('h2',{text:'Ganti PIN admin'}),
    field('pin-baru','PIN baru (minimal 6 karakter)',pinI),
    h('div',{class:'actions'}, h('button',{class:'btn ghost',type:'submit',text:'Ganti PIN'})));
  fP.addEventListener('submit', async function(e){
    e.preventDefault();
    if (pinI.value.length < 6){ toast('PIN minimal 6 karakter.'); return; }
    try { state.settings.pinHash = await sha(pinI.value); pinI.value = ''; touch(); toast('PIN diganti. Simpan perubahan agar berlaku untuk semua admin.'); }
    catch (err){ toast('Browser ini tidak mendukung penggantian PIN.'); }
  });
  wrap.append(fP);
  return wrap;
}

function renderAdmin(){
  var root = $('#adminView'); root.replaceChildren();
  var tabs = h('div',{class:'tabs',role:'tablist','aria-label':'Kelola kas'});
  var TABS = [['transaksi','Transaksi'],['bukti','Bukti pembayaran'],['iuran','Iuran']];
  TABS.forEach(function(x){
    tabs.append(h('button',{class:'tab',role:'tab',type:'button',id:'tab-' + x[0],'aria-selected':adminTab === x[0] ? 'true' : 'false','aria-controls':'panel',tabindex:adminTab === x[0] ? '0' : '-1',text:x[1],
      onclick:function(){ adminTab = x[0]; renderAdmin(); },
      onkeydown:function(e){ if (e.key === 'ArrowRight' || e.key === 'ArrowLeft'){ var i = TABS.map(function(t){ return t[0]; }).indexOf(adminTab); i = (i + (e.key === 'ArrowRight' ? 1 : TABS.length - 1)) % TABS.length; adminTab = TABS[i][0]; renderAdmin(); $('#tab-' + adminTab).focus(); } }}));
  });
  var panel = h('div',{id:'panel',role:'tabpanel','aria-labelledby':'tab-' + adminTab});
  if (adminTab === 'transaksi'){
    var st = h('div',{class:'stack'}, trxForm(), trxList());
    if (state.settings.demo){
      st.append(h('div',{class:'card'}, h('h2',{text:'Data contoh masih aktif'}),
        h('p',{text:'Transaksi yang tampil sekarang hanya contoh. Kosongkan sebelum mulai mencatat kas yang sebenarnya.'}),
        h('div',{class:'actions'}, h('button',{class:'btn coin',type:'button',text:'Kosongkan data contoh',onclick:async function(){
          if (await confirmBox('Hapus semua transaksi contoh dan sembunyikan pemberitahuan data contoh?', 'Ya, kosongkan')){
            state.transaksi = []; state.settings.demo = false; editTrx = null; touch(); renderAdmin(); toast('Data contoh dikosongkan.');
          }
        }}))));
    }
    panel.append(st);
  } else if (adminTab === 'bukti'){
    panel.append(h('div',{class:'stack'}, buktiForm(), buktiList()));
  } else panel.append(iuranTab());
  root.append(h('section',{class:'adm'},
    h('div',{class:'adm-head'},
      h('h1',{text:'Halo, ' + (admin ? admin.nama.split(' ')[0] : 'admin')}),
      h('div',{class:'btns',style:'display:flex;gap:8px;flex-wrap:wrap'}, h('button',{class:'btn ghost',type:'button',text:'Lihat halaman publik',onclick:function(){ setMode('public'); }}))),
    tabs, panel));
}

/* ---------- mode (public/admin di dalam appShell) ---------- */
function setMode(m){
  mode = m;
  $('#publicView').hidden = m !== 'public';
  $('#adminView').hidden = m !== 'admin';
  document.body.classList.toggle('in-admin', m === 'admin');
  var navAdmin = $('#navAdminLink');
  if (navAdmin) navAdmin.hidden = !(currentRole === 'admin' && m === 'public');
  if (m === 'admin') renderAdmin();
  updateSaveBar(); window.scrollTo({top:0});
}

/* ---------- gerbang login (wajib sebelum melihat dashboard) ---------- */
function showGate(){
  $('#appShell').hidden = true;
  $('#gate').hidden = false;
  document.body.classList.remove('in-admin');
  var f = $('#gNim'), p = $('#gPass'), err = $('#gateErr');
  if (f) f.value = ''; if (p) p.value = ''; if (err) err.hidden = true;
  setTimeout(function(){ try { f.focus(); } catch (e) {} }, 0);
}

function enterApp(role, nim){
  currentRole = role; currentNim = nim;
  var who = '';
  if (role === 'admin'){
    admin = state.admins.filter(function(a){ return a.nim === nim; })[0] || {nama:'Bendahara', nim:nim, role:'Bendahara'};
    who = 'Masuk sebagai ' + admin.nama + ' (Bendahara)';
    mode = 'admin';
  } else {
    admin = null;
    var m = state.mahasiswa.filter(function(x){ return x.nim === nim; })[0];
    who = 'Masuk sebagai ' + (m ? m.nama : nim);
    mode = 'public';
  }
  try { sessionStorage.setItem(AUTH_KEY, JSON.stringify({nim:nim, role:role})); } catch (e) {}
  var wEl = $('#whoami'); if (wEl) wEl.textContent = who;
  $('#gate').hidden = true;
  $('#appShell').hidden = false;
  setMode(mode);
}

function logout(){
  if (dirty && !window.confirm('Ada perubahan yang belum disimpan. Keluar tanpa menyimpan?')) return;
  admin = null; currentRole = null; currentNim = null; dirty = false;
  try { sessionStorage.removeItem(AUTH_KEY); } catch (e) {}
  showGate();
}

$('#authBtn').addEventListener('click', logout);
$('#brandLink').addEventListener('click', function(e){ e.preventDefault(); setMode('public'); });
var navAdminLink = $('#navAdminLink');
if (navAdminLink) navAdminLink.addEventListener('click', function(e){ e.preventDefault(); setMode('admin'); });

$('#gateForm').addEventListener('submit', async function(e){
  e.preventDefault();
  var nim = ($('#gNim').value || '').trim();
  var pass = $('#gPass').value || '';
  var err = $('#gateErr');
  err.hidden = true;

  if (nim && nim === state.settings.treasurerNim){
    try {
      var ok = (await sha(pass)) === state.settings.pinHash;
      if (ok){ enterApp('admin', nim); return; }
    } catch (x){
      err.textContent = 'Browser ini tidak mendukung login. Buka lewat https.'; err.hidden = false; return;
    }
    err.textContent = 'Password bendahara salah.'; err.hidden = false; return;
  }

  var m = state.mahasiswa.filter(function(x){ return x.nim === nim; })[0];
  if (m && pass === nim){ enterApp('user', nim); return; }

  err.textContent = 'NIM atau password salah. Pastikan NIM terdaftar dan password sama dengan NIM.';
  err.hidden = false;
});

/* ---------- save ---------- */
function buildDoc(){
  var json = JSON.stringify(state).replace(/</g, '\\u003c');
  return BASE_HTML.replace(/(<script type="application\/json" id="kas-data">)[\s\S]*?(<\/script>)/, function(m, a, b){ return a + json + b; });
}
$('#saveBtn').addEventListener('click', async function(){
  if (!dirty) return;
  var btn = $('#saveBtn'); btn.disabled = true;
  if (artifactNS){
    try {
      await artifactNS.publish(buildDoc());
      dirty = false; try { localStorage.removeItem(DRAFT_KEY); } catch (e) {}
      updateSaveBar(); toast('Tersimpan. Halaman dimuat ulang dengan data terbaru.');
    } catch (err){
      var c = err && err.code;
      if (c === 'not_granted' || c === 'not_writer') toast('Akun ini tidak punya izin menyimpan ke website. Simpan dari akun pemilik halaman.');
      else if (c === 'conflict') toast('Ada versi yang lebih baru dari admin lain. Halaman dimuat ulang, ulangi perubahanmu.');
      else toast('Gagal menyimpan. Coba lagi sebentar lagi.');
    }
  } else {
    try { localStorage.setItem(DRAFT_KEY, JSON.stringify(state)); dirty = false; updateSaveBar(); toast('Disimpan di perangkat ini. Mahasiswa lain baru melihatnya setelah website diterbitkan ulang.'); }
    catch (e){ toast('Penyimpanan di perangkat ini tidak tersedia.'); }
  }
  btn.disabled = false;
});
window.addEventListener('beforeunload', function(e){ if (dirty){ e.preventDefault(); e.returnValue = ''; } });

/* ---------- init ---------- */
var qTimer;
$('#q').addEventListener('input', function(){ clearTimeout(qTimer); qTimer = setTimeout(renderIuran, 120); });
document.querySelectorAll('.chip').forEach(function(c){
  c.addEventListener('click', function(){
    filterJenis = c.getAttribute('data-f');
    document.querySelectorAll('.chip').forEach(function(x){ x.setAttribute('aria-pressed', x === c ? 'true' : 'false'); });
    renderRiwayat();
  });
});
renderPublic();
var authed = findAuthed();
if (authed) enterApp(authed.role, authed.nim); else showGate();
(async function(){
  try {
    if (window.claude && typeof window.claude.use === 'function'){
      artifactNS = await window.claude.use('artifact');
      updateSaveBar();
    }
  } catch (e) { artifactNS = null; }
})();
})();