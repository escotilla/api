<?php

namespace App\Factories;

use App\Application;
use App\ChecklistItem;

class ChecklistFactory
{
    public static function createChecklist(Application $application, array $titleArray)
    {
        foreach ($titleArray as $title) {
            $checklistItem = new ChecklistItem(['title' => $title, 'status' => 'incomplete']);
            $application->checklist()->associate($checklistItem);
        }

        return $application;
    }
}