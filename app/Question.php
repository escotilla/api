<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Jenssegers\Mongodb\Relations\HasMany;

/**
 * Class Question
 * @package App
 * @property string short_question
 * @property string english
 * @property string spanish
 * @property string category
 * @property string answer_type
 * @property string attribute
 */
class Question extends Eloquent
{
    const LOAN_AMOUNT = '5a54031fbfaf5700a0017672';

    const BUSINESS_DESCRIPTION = '5a501d0ebfaf57001c265b22';
    const BUSINESS_PRODUCT = '5a5287a5bfaf57008c058792';
    const BUSINESS_NAME = '5a5287a5bfaf57008c058793';

    const DOB = '5a501d0ebfaf57001c265b23';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'short_question',
        'english',
        'spanish',
        'category',
        'answer_type',
        'attribute'
    ];

    public function to_public_output(): array
    {
        $output = [
            'question_id' => $this->_id,
            'short_question' => $this->short_question,
            'english' => $this->english,
            'spanish' => $this->spanish,
            'category' => $this->category,
            'answer_type' => $this->answer_type,
            'attribute' => $this->attribute,
        ];

        return $output;
    }
}
