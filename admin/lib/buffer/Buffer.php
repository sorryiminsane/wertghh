<?php
/*
 * This file is a part of "charcoal-dev/buffers" package.
 * https://github.com/charcoal-dev/buffers
 *
 * Copyright (c) Furqan A. Siddiqui <hello@furqansiddiqui.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code or visit following link:
 * https://github.com/charcoal-dev/buffers/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Charcoal\Buffers;

/**
 * Class Buffer
 * @package Charcoal\Buffers
 */
class Buffer extends AbstractWritableBuffer
{
    /**
     * @param string|null $data
     */
    public function __construct(?string $data = null)
    {
        parent::__construct($data);
        $this->readOnly = false;
    }
}

