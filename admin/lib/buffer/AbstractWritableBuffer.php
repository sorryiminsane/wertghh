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

use Charcoal\Buffers\ByteOrder\AbstractEndianness;
use Charcoal\Buffers\ByteOrder\BigEndian;
use Charcoal\Buffers\ByteOrder\LittleEndian;

/**
 * Class AbstractWritableBuffer
 * @package Charcoal\Buffers
 */
abstract class AbstractWritableBuffer extends AbstractByteArray
{
    /**
     * @return $this
     */
    public function readOnly(): static
    {
        $this->readOnly = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function writable(): static
    {
        $this->readOnly = false;
        return $this;
    }

    /**
     * @return bool
     */
    public function isWritable(): bool
    {
        return !$this->readOnly;
    }

    /**
     * @return $this
     */
    public function flush(): self
    {
        $this->checkWritable();
        $this->data = "";
        $this->len = 0;
        return $this;
    }

    /**
     * @param AbstractByteArray|string|null $bytes
     * @return $this
     */
    public function append(AbstractByteArray|string|null $bytes): static
    {
        $this->checkWritable();
        $bytes = $bytes instanceof AbstractByteArray ? $bytes->raw() : $bytes;
        if (is_string($bytes) && strlen($bytes)) {
            $this->setBuffer($this->data . $bytes);
        }

        return $this;
    }

    /**
     * @param AbstractByteArray|string|null $bytes
     * @return $this
     */
    public function prepend(AbstractByteArray|string|null $bytes): static
    {
        $this->checkWritable();
        $bytes = $bytes instanceof AbstractByteArray ? $bytes->raw() : $bytes;
        if (is_string($bytes) && strlen($bytes)) {
            $this->setBuffer($bytes . $this->data);
        }

        return $this;
    }

    /**
     * @param int $uint
     * @return $this
     */
    public function prependUInt8(int $uint): static
    {
        $this->checkWritable();
        $this->data = AbstractEndianness::PackUInt8($uint) . $this->data;
        $this->len++;
        return $this;
    }

    /**
     * @param int $uint
     * @return $this
     */
    public function appendUInt8(int $uint): static
    {
        $this->checkWritable();
        $this->data .= AbstractEndianness::PackUInt8($uint);
        $this->len++;
        return $this;
    }

    /**
     * @param int $uint
     * @return $this
     */
    public function appendUInt16LE(int $uint): static
    {
        $this->checkWritable();
        $this->data .= LittleEndian::PackUInt16($uint);
        $this->len += 2;
        return $this;
    }

    /**
     * @param int $uint
     * @return $this
     */
    public function appendUInt16BE(int $uint): static
    {
        $this->checkWritable();
        $this->data .= BigEndian::PackUInt16($uint);
        $this->len += 2;
        return $this;
    }

    /**
     * @param int $uint
     * @return $this
     */
    public function appendUInt32LE(int $uint): static
    {
        $this->checkWritable();
        $this->data .= LittleEndian::PackUInt32($uint);
        $this->len += 4;
        return $this;
    }

    /**
     * @param int $uint
     * @return $this
     */
    public function appendUInt32BE(int $uint): static
    {
        $this->checkWritable();
        $this->data .= BigEndian::PackUInt32($uint);
        $this->len += 4;
        return $this;
    }

    /**
     * @param int|string $uint
     * @return $this
     */
    public function appendUInt64LE(int|string $uint): static
    {
        $this->checkWritable();
        $this->data .= LittleEndian::PackUInt64($uint);
        $this->len += 8;
        return $this;
    }

    /**
     * @param int|string $uint
     * @return $this
     */
    public function appendUInt64BE(int|string $uint): static
    {
        $this->checkWritable();
        $this->data .= BigEndian::PackUInt64($uint);
        $this->len += 8;
        return $this;
    }

    /**
     * @return void
     */
    protected function checkWritable(): void
    {
        if ($this->readOnly) {
            throw new \BadMethodCallException('Buffer is in readonly state');
        }
    }
}