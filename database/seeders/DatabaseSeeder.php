<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Clear cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions Array - Organized by module
        $permissions = [
            // User Management
            'user.create',
            'user.delete', 
            'user.show',
            'user.update',
            'user.manage',
            
            // Doctor Management
            'doctor.create',
            'doctor.delete',
            'doctor.show',
            'doctor.update',
            
            // Patient Management
            'patient.create',
            'patient.delete',
            'patient.show',
            'patient.update',
            
            // Appointment Management
            'appointment.create',
            'appointment.delete',
            'appointment.show',
            'appointment.update',
            'appointment.approve',
            'appointment.cancel',
            
            // Feedback Management
            'feedback.create',
            'feedback.delete',
            'feedback.show',
            'feedback.update',
            
            // Medical Records
            'medical_record.create',
            'medical_record.delete',
            'medical_record.show',
            'medical_record.update',
            
            // Lab Management
            'lab_order.create',
            'lab_order.view',
            'lab_result.create',
            'lab_result.view',
            
            // Prescription Management
            'prescription.create',
            'prescription.delete',
            'prescription.show',
            'prescription.update',
            
            // System Administration
            'system.settings',
            'reports.view',
            'audit.logs',
        ];

        // Create Permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Admin role and permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo($permissions); // All permissions

        // Doctor role and permissions
        $doctorRole = Role::firstOrCreate(['name' => 'doctor']);
        $doctorRole->givePermissionTo([
            // Patient permissions
            'patient.create',
            'patient.show',
            'patient.update',
            
            // Appointment permissions
            'appointment.create',
            'appointment.delete', 
            'appointment.show',
            'appointment.update',
            'appointment.approve',
            'appointment.cancel',
            
            // Medical records
            'medical_record.create',
            'medical_record.show',
            'medical_record.update',
            
            // Lab permissions
            'lab_order.create',
            'lab_order.view',
            'lab_result.create',
            'lab_result.view',
            
            // Prescription permissions
            'prescription.create',
            'prescription.show',
            'prescription.update',
            
            // Feedback
            'feedback.show',
        ]);

        // Patient role and permissions
        $patientRole = Role::firstOrCreate(['name' => 'patient']);
        $patientRole->givePermissionTo([
            'feedback.create',
            'feedback.delete',
            'feedback.show',
            'feedback.update',
            'doctor.show',
            'appointment.show',
            'appointment.create',
            'appointment.cancel',
            'medical_record.show',
            'lab_result.view',
            'prescription.show',
        ]);

        // Staff role and permissions (optional)
        $staffRole = Role::firstOrCreate(['name' => 'staff']);
        $staffRole->givePermissionTo([
            'patient.create',
            'patient.show',
            'patient.update',
            'appointment.create',
            'appointment.show',
            'appointment.update',
            'doctor.show',
            'feedback.show',
        ]);

        // Create demo users (optional)
        $this->createDemoUsers();
    }

    /**
     * Create demo users for testing
     */
    private function createDemoUsers(): void
    {
        // Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@hmis.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // Doctor User
        $doctor = User::firstOrCreate(
            ['email' => 'doctor@hmis.com'],
            [
                'name' => 'Dr. John Smith',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $doctor->assignRole('doctor');

        // Patient User
        $patient = User::firstOrCreate(
            ['email' => 'patient@hmis.com'],
            [
                'name' => 'Jane Doe',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $patient->assignRole('patient');

        // Staff User
        $staff = User::firstOrCreate(
            ['email' => 'staff@hmis.com'],
            [
                'name' => 'Receptionist',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $staff->assignRole('staff');
    }
}