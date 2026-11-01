<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateStudentAccounts extends Command
{
    protected $signature = 'users:create-students';
    protected $description = 'Create student accounts from the provided list';

    private $students = [
        ['name' => 'Pahayahay, Juvelyn G.', 'email' => 'pahayahay.juvelyn@example.com'],
        ['name' => 'Jumachag, Judy Ann L.', 'email' => 'jumachag.judy@example.com'],
        ['name' => 'Jimenez, Almeje E.', 'email' => 'jimenez.almeje@example.com'],
        ['name' => 'Cuyos, Honey Faith L.', 'email' => 'cuyos.honey@example.com'],
        ['name' => 'Linggas, Angell Maye J.', 'email' => 'linggas.angell@example.com'],
        ['name' => 'Rusiana, Mark Diric S.', 'email' => 'rusiana.mark@example.com'],
        ['name' => 'Halipa, Bhailyn N.', 'email' => 'halipa.bhailyn@example.com'],
        ['name' => 'Soco, Diane Marie S.', 'email' => 'soco.diane@example.com'],
        ['name' => 'Amerol, Jalal P.', 'email' => 'amerol.jalal@example.com'],
        ['name' => 'Agac-ac, Sharen J.', 'email' => 'agacac.sharen@example.com'],
        ['name' => 'Caresma, Zirel', 'email' => 'caresma.zirel@example.com'],
        ['name' => 'Pano, Kim Kara F.', 'email' => 'pano.kim@example.com'],
        ['name' => 'Andales, Cliff Gerald T.', 'email' => 'andales.cliff@example.com'],
        ['name' => 'Lareta, Kwenny Jane B.', 'email' => 'lareta.kwenny@example.com'],
        ['name' => 'Ebo, Norbert M.', 'email' => 'ebo.norbert@example.com'],
        ['name' => 'Bital, Joshua Cleese B.', 'email' => 'bital.joshua@example.com'],
        ['name' => 'Rondina, Argie M.', 'email' => 'rondina.argie@example.com'],
        ['name' => 'Pahamutang, Jotham L.', 'email' => 'pahamutang.jotham@example.com'],
        ['name' => 'Sumaylo, John Mark D.', 'email' => 'sumaylo.john@example.com'],
        ['name' => 'Baterna, Jaycer A.', 'email' => 'baterna.jaycer@example.com'],
        ['name' => 'Naparan, Hyacinth C.', 'email' => 'naparan.hyacinth@example.com'],
        ['name' => 'Baguan, Norhainah B.', 'email' => 'baguan.norhainah@example.com'],
        ['name' => 'Brade, Ian R.', 'email' => 'brade.ian@example.com'],
        ['name' => 'Bello, Louise Victor L.', 'email' => 'bello.louise@example.com'],
        ['name' => 'Mangayao, John Rey B.', 'email' => 'mangayao.john@example.com'],
        ['name' => 'Calig-onan, Joryl John P.', 'email' => 'caligonan.joryl@example.com'],
        ['name' => 'Huminig, Niña Faye B.', 'email' => 'huminig.nina@example.com'],
        ['name' => 'Gapor, Ryan Dave C.', 'email' => 'gapor.ryan@example.com'],
        ['name' => 'Balaod, Krizzia R.', 'email' => 'balaod.krizzia@example.com'],
        ['name' => 'Tee, Yuri T.', 'email' => 'tee.yuri@example.com'],
        ['name' => 'Fernandez, Arman Roy D.', 'email' => 'fernandez.arman@example.com'],
        ['name' => 'Abanales, Sean Christlet C.', 'email' => 'abanales.sean@example.com'],
        ['name' => 'Gabule, John Raymond E.', 'email' => 'gabule.john@example.com'],
        ['name' => 'Auza, Jayvin B.', 'email' => 'auza.jayvin@example.com'],
        ['name' => 'Gayongan, Melchie-Ann A.', 'email' => 'gayongan.melchie@example.com'],
        ['name' => 'Estaño, Jerald A.', 'email' => 'estano.jerald@example.com'],
        ['name' => 'Lagura, Adrian Paul B.', 'email' => 'lagura.adrian@example.com'],
        ['name' => 'Sumonod, Mary Claire R.', 'email' => 'sumonod.mary@example.com'],
        ['name' => 'Villabeto, Aimie D.', 'email' => 'villabeto.aimie@example.com'],
        ['name' => 'Cagatin, Mark jed M.', 'email' => 'cagatin.mark@example.com'],
    ];

    public function handle()
    {
        $password = Hash::make('qwertyuiop');
        $created = 0;
        $skipped = 0;

        foreach ($this->students as $student) {
            // Check if user already exists
            if (User::where('email', $student['email'])->exists()) {
                $this->warn("Skipped: {$student['name']} ({$student['email']}) - already exists");
                $skipped++;
                continue;
            }

            // Create user with student role
            User::create([
                'name' => $student['name'],
                'email' => $student['email'],
                'password' => $password,
                'role' => 'student',
                'email_verified_at' => now(), // Auto-verify for convenience
            ]);

            $this->info("Created: {$student['name']} ({$student['email']})");
            $created++;
        }

        $this->newLine();
        $this->info("=== Summary ===");
        $this->info("Total students: " . count($this->students));
        $this->info("Created: $created");
        $this->info("Skipped: $skipped");
        $this->newLine();
        $this->info("All accounts have password: qwertyuiop");

        return 0;
    }
}