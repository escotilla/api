<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Jenssegers\Mongodb\Relations\HasMany;

/**
 * Class Question
 * @package App
 * @property string short_question
 * @property string input_type
 * @property string category
 * @property string answer_type
 */
class Question extends Eloquent
{
    const LOAN_AMOUNT = '5a54031fbfaf5700a0017672';
    const INFO_ACCURATE = '5a7e03f9921ef3001409e164';
    const AGREE_LOAN_CONTRACT = '5a7e670b921ef3001c00ace2';

    const BUSINESS_DESCRIPTION = '5a501d0ebfaf57001c265b22';
    const BUSINESS_PRODUCT = '5a5287a5bfaf57008c058792';
    const BUSINESS_NAME = '5a5287a5bfaf57008c058793';

    const DOB = '5a501d0ebfaf57001c265b23';
    const FULL_NAME = '5a7e03f9921ef3001409e162';
    const EMAIL = '5a7e03f9921ef3001409e163';
    const PASSWORD = '5a86577f921ef3000f56b0a2';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'short_question',
        'category',
        'answer_type',
        'input_type'
    ];

    public function to_public_output(): array
    {
        $output = [
            'question_id' => $this->_id,
            'short_question' => $this->short_question,
            'input_type' => $this->input_type,
            'category' => $this->category,
            'answer_type' => $this->answer_type,
        ];

        return $output;
    }
}
