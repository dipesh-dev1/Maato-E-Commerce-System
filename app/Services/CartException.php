<?php
declare(strict_types=1);

namespace App\Services;

/** A problem the shopper can act on (sold out, quantity too high, invalid code). The message is shown as-is. */
final class CartException extends \RuntimeException
{
}
