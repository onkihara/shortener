<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Shortener extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'CreationDate';

    const UPDATED_AT = 'UpdateDate';

    const DELETED_AT = 'DeleteDate';

    /**
     * Custom table name
     */
    protected $table = 'shortcuts';

    /**
     * Set primary-key-name
     */
    protected $primaryKey = 'ID';

    /**
     * Mass assignable
     */
    protected $fillable = ['UserID', 'Hash', 'Url', 'Description', 'Frequency', 'PublishDate', 'ExpirationDate'];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'CreationDate' => 'datetime',
            'UpdateDate' => 'datetime',
            'PublishDate' => 'datetime',
            'ExpirationDate' => 'datetime',
            'DeleteDate' => 'datetime',
        ];
    }

    /**
     * Get the User for this entry
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'id');
    }

    /**
     * Generate a unique hash automatically
     */
    public function generateHash(?string $customHash = null): string
    {
        // Wenn ein custom Hash angegeben wurde, validieren
        if ($customHash) {
            $customHash = Str::slug($customHash);

            // Validiere, dass der Hash nicht bereits existiert
            if (! $this->hashExists($customHash)) {
                return $customHash;
            }

            throw new \Exception('Hash "'.$customHash.'" is already taken.');
        }

        // Automatische Hash-Generierung
        $lengths = config('shortener.hash.lengths', [6, 7, 8]);
        $retrials = config('shortener.hash.retrials', 10);
        $characters = config('shortener.hash.characters', 'abcdefghijklmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789');

        foreach ($lengths as $length) {
            for ($i = 0; $i < $retrials; $i++) {
                $hash = $this->generateRandomString($length, $characters);

                if (! $this->hashExists($hash)) {
                    return $hash;
                }
            }
        }

        throw new \Exception('Could not generate a unique hash after multiple attempts.');
    }

    /**
     * Check if hash already exists in database
     */
    private function hashExists(string $hash): bool
    {
        return self::withTrashed()
            ->where('Hash', $hash)
            ->exists();
    }

    /**
     * Generate a random string of specified length
     */
    private function generateRandomString(int $length, string $characters): string
    {
        $result = '';
        $charLength = strlen($characters);

        for ($i = 0; $i < $length; $i++) {
            $result .= $characters[random_int(0, $charLength - 1)];
        }

        return $result;
    }

    /**
     * Scope: Get only active (not deleted) shorteners
     */
    public function scopeActive($query)
    {
        return $query->whereNull('DeleteDate');
    }

    /**
     * Scope: Get only published shorteners (or without publish date) and not expired
     */
    public function scopePublished($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('PublishDate')
                ->orWhere('PublishDate', '<=', now());
        })->where(function ($q) {
            $q->whereNull('ExpirationDate')
                ->orWhere('ExpirationDate', '>', now());
        });
    }

    /**
     * Scope: Get shorteners for a specific user
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('UserID', $userId);
    }

    /**
     * Check if shortener is published
     */
    public function isPublished(): bool
    {
        return ! $this->PublishDate || $this->PublishDate <= now();
    }

    /**
     * Check if shortener is expired
     */
    public function isExpired(): bool
    {
        return $this->ExpirationDate && $this->ExpirationDate <= now();
    }

    /**
     * Increment frequency when accessed
     */
    public function recordAccess(): void
    {
        $this->increment('Frequency');
    }
}
