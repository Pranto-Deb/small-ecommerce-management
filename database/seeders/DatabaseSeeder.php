<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
class DatabaseSeeder extends Seeder {
 public function run(): void {
  $email=env('ADMIN_EMAIL');$password=env('ADMIN_PASSWORD');
  if (!$email || !$password || strlen($password)<8) throw new RuntimeException('Set ADMIN_EMAIL and ADMIN_PASSWORD (minimum 8 characters) in .env before seeding.');
  $user=User::firstOrNew(['email'=>$email]);
  if (!$user->exists) { $user->name=env('ADMIN_NAME','Administrator');$user->password=Hash::make($password);$user->role='admin';$user->save(); }
 }
}
