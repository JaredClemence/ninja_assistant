<?php

namespace App\Http\Controllers\AbstractFactory\CsvLines;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $format
 * @property string $label
 * @property string $parser
 */
class FileFormat extends Model
{
    protected $fillable = [
        'format',
        'label',
        'parser'
    ];
}
