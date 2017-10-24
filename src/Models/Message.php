<?php

namespace BaklySystems\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'conversation_id',
        'user_id',
        'message',
        'is_seen',
        'deleted_from_sender',
        'deleted_from_receiver'
    ];

    /**
     * The rules attributes.
     *
     * @var array
     */
    protected static $rules = [
        'conversation_id'       => 'required|integer',
        'user_id'               => 'required|integer',
        'message'               => 'string',
        'is_seen'               => 'boolean',
        'deleted_from_sender'   => 'boolean',
        'deleted_from_receiver' => 'boolean'
    ];

    /**
     * The rules getter.
     *
     * @return array
     */
    public static function rules()
    {
        return self::$rules;
    }

    /**
     * Get message conversation.
     *
     * @return collection
     */
    public function conversation()
    {
        return $this->belongsTo('BaklySystems\LaravelMessenger\Models\Conversation');
    }

    /**
     * Get message sender.
     *
     * @return collection
     */
    public function sender()
    {
        return $this->belongsTo(config('messenger.user.model', 'App\User'));
    }
}
