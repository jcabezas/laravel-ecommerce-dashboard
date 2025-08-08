<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * Class Store
 *
 * Representa una tienda de e-commerce conectada por un usuario.
 * Este modelo es responsable de almacenar de forma segura las credenciales
 * necesarias para la integración con las APIs externas de WooCommerce y Shopify.
 */
class Store extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'platform',
        'store_url',
        'api_key',
        'api_secret',
        'access_token',
    ];

    /**
     * Define la relación inversa de uno a muchos con el modelo User.
     * Una tienda pertenece a un único usuario.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor y Mutator para el atributo 'api_key'.
     *
     * get: Desencripta el valor al acceder a él.
     * set: Encripta el valor antes de guardarlo en la base de datos.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function apiKey(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Crypt::decryptString($value),
            set: fn($value) => Crypt::encryptString($value),
        );
    }

    /**
     * Accessor y Mutator para el atributo 'api_secret'.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function apiSecret(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Crypt::decryptString($value),
            set: fn($value) => Crypt::encryptString($value),
        );
    }

    /**
     * Accessor y Mutator para el atributo 'access_token' de Shopify.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function accessToken(): Attribute
    {
        return Attribute::make(
            get: fn($value) => is_null($value) ? null : Crypt::decryptString($value),
            set: fn($value) => is_null($value) ? null : Crypt::encryptString($value),
        );
    }
}
