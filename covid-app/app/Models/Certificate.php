<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    const MAX_UPLOAD_ATTEMPTS = 5;
    const ALLOWED_FILE_EXT = '/(png|jpg|jpeg|webp|bmp)/i';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'certificates';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ref', // UUID
        'status', // ENUM(approved | rejected | pending)
        'user_id' // Foreign key
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    // protected $with = ['user'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
