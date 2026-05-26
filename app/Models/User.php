<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'nim',
        'nip',
        'username',
        'program_studi',
        'first_name',
        'last_name',
        'phone_number',
        'user_type',
        'role',
        'status',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user has specific role
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Check if user is mahasiswa
     */
    public function isMahasiswa()
    {
        return $this->hasRole('mahasiswa');
    }

    /**
     * Check if user is dosen
     */
    public function isDosen()
    {
        return $this->hasRole('dosen');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    /**
     * Relasi ke penilaian sebagai mahasiswa
     */
    public function penilaianAsMahasiswa()
    {
        return $this->hasMany(Penilaian::class, 'mahasiswa_id');
    }

    /**
     * Relasi ke penilaian sebagai dosen
     */
    public function penilaianAsDosen()
    {
        return $this->hasMany(Penilaian::class, 'dosen_id');
    }

    /**
     * Get penilaian for this user (as mahasiswa)
     */
    public function getPenilaian()
    {
        return $this->penilaianAsMahasiswa()->first();
    }

    public function ledGroups()
    {
        return $this->hasMany(Group::class, 'leader_id');
    }

    public function supervisedGroups()
    {
        return $this->hasMany(Group::class, 'dosen_id');
    }
}

