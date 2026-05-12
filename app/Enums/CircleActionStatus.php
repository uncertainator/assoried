<?php

namespace App\Enums;

enum CircleActionStatus: string
{
    case Todo = 'todo';
    case InProgress = 'in_progress';
    case Done = 'done';

    public function label(): string
    {
        return match ($this) {
            self::Todo => 'À faire',
            self::InProgress => 'En cours',
            self::Done => 'Terminé',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Todo => 'fb-badge-ocre',
            self::InProgress => 'fb-badge-brique',
            self::Done => 'fb-badge-mousse',
        };
    }
}
