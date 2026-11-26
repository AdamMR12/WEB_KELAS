<?php

namespace Tests\Feature;

use App\Models\AgendaCategory;
use App\Models\AgendaStatus;
use App\Models\JadwalAgenda;
use App\Models\JadwalMk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JadwalIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_mahasiswa_can_view_jadwal_and_agenda_but_cannot_mutate(): void
    {
        $this->seed();
        $mahasiswa = User::where('level', User::LEVEL_USER)->firstOrFail();

        $this->actingAs($mahasiswa)
            ->get('/jadwal?tanggal=2026-11-25')
            ->assertOk()
            ->assertSee('Pemrograman Web')
            ->assertDontSee('Tambah Jadwal');

        $this->actingAs($mahasiswa)
            ->get('/agenda')
            ->assertOk()
            ->assertSee('Hackathon Inovasi Digital Mahasiswa 2026');

        $this->actingAs($mahasiswa)
            ->get('/jadwal/create')
            ->assertForbidden();
    }

    public function test_admin_can_create_and_delete_jadwal_and_agenda_with_erd_relations(): void
    {
        $this->seed();
        $admin = User::where('level', User::LEVEL_ADMIN)->firstOrFail();

        $this->actingAs($admin)
            ->post('/jadwal', [
                'nama_matakuliah' => 'Keamanan Siber',
                'sks' => 3,
                'hari' => 'Kamis',
                'tanggal' => '2026-11-26',
                'jam_mulai' => '13:00',
                'jam_selesai' => '15:00',
                'ruang' => 'Lab Keamanan 1',
                'dosen' => 'Pak Budi Santoso, M.Kom.',
                'jenis' => 'Mata Kuliah Wajib',
                'metode' => 'Praktikum Lab',
                'warna_bg' => 'emerald',
                'is_pengganti' => 0,
            ])
            ->assertRedirect(route('jadwal.index', ['tanggal' => '2026-11-26']));

        $jadwal = JadwalMk::whereHas('mataKuliah', fn ($q) => $q->where('nama_mk', 'Keamanan Siber'))->firstOrFail();
        $this->assertSame('Lab Keamanan 1', $jadwal->ruang);
        $this->assertSame('Pak Budi Santoso, M.Kom.', $jadwal->dosen);

        $this->actingAs($admin)
            ->delete(route('jadwal.destroy', $jadwal))
            ->assertRedirect();

        $this->assertDatabaseMissing('jadwal_mk', ['id_jadwal' => $jadwal->id_jadwal]);

        $catKode = AgendaCategory::firstOrFail()->kode;
        $statusNama = AgendaStatus::firstOrFail()->nama;

        $this->actingAs($admin)
            ->post('/agenda', [
                'judul' => 'Kumpul Kelas SI-2A',
                'kategori' => $catKode,
                'status' => $statusNama,
                'tanggal_mulai' => '2026-11-28',
                'waktu' => '16:00 WIB',
                'lokasi' => 'Ruang Teori 201',
                'koordinator' => 'Ketua Kelas',
            ])
            ->assertRedirect(route('agenda.index', ['kategori' => $catKode]));

        $agenda = JadwalAgenda::whereHas('agendaNonAkademik', fn ($q) => $q->where('nama_agenda', 'Kumpul Kelas SI-2A'))->firstOrFail();
        $this->assertSame('Ruang Teori 201', $agenda->lokasi);

        $this->actingAs($admin)
            ->delete(route('agenda.destroy', $agenda))
            ->assertRedirect();

        $this->assertDatabaseMissing('jadwal_agenda', ['id_jadwal' => $agenda->id_jadwal]);
    }
}
