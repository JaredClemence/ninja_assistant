<?php

namespace App\Http\Controllers\AbstractFactory\CsvFiles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AbstractFactory\CsvFiles\AbstractController;

/**
 * The abstract factory returns this controller for all null values. It does nothing.
 */
class NullController extends AbstractController
{
}
