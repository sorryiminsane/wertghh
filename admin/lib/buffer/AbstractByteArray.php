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

use Charcoal\Adapters\GMP\BuffersBridgeInterface;

/**
 * Class AbstractByteArray
 * @package Charcoal\Buffers
 */
abstract class AbstractByteArray implements BuffersBridgeInterface, \Stringable
{
    /** @var string */
    protected string $data = "";
    /** @var int */
    protected int $len = 0;
    /** @var bool */
    protected bool $readOnly = true;

    /**
     * @param string|null $data
     */
    public function __construct(?string $data = null)
    {
        if ($data) {
            $this->setBuffer($data);
        }
    }

    /**
     * @param string $bytes
     */
    protected function setBuffer(string $bytes): void
    {
        $this->data = $bytes;
        $this->len = strlen($this->data);
    }

    /**
     * @param string $hex
     * @return static
     */
    public static function fromBase16(string $hex): static
    {
        // Validate string as Hexadecimal
        if (!preg_match('/^(0x)?[a-f0-9]+$/i', $hex)) {
            throw new \InvalidArgumentException('Cannot instantiate Buffer; expected Base16/Hexadecimal string');
        }

        // Remove the "0x" prefix
        if (str_starts_with($hex, "0x")) {
            $hex = substr($hex, 2);
        }

        // Evens-out odd number of hexits
        if (strlen($hex) % 2 !== 0) {
            $hex = "0" . $hex;
        }

        return new static(hex2bin($hex));
    }

    /**
     * @return string
     */
    public function toBase16(): string
    {
        $hexits = bin2hex($this->raw());
        if (strlen($hexits) % 2 !== 0) {
            $hexits = "0" . $hexits;
        }
        return $hexits;
    }

    /**
     * @param string $b64
     * @return static
     */
    public static function fromBase64(string $b64): static
    {
        $bytes = base64_decode($b64, true);
        if (!$bytes) {
            throw new \InvalidArgumentException('Cannot instantiate Buffer; Invalid base64 encoded data');
        }

        return new static($bytes);
    }

    /**
     * @param array $bA
     * @return static
     */
    public static function fromByteArray(array $bA): static
    {
        $i = -1;
        $str = "";
        foreach ($bA as $byte) {
            $i++;
            if (!is_int($byte) || $byte < 0 || $byte > 0xff) {
                throw new \InvalidArgumentException(sprintf('Invalid byte at index %d', $i));
            }

            $str .= chr($byte);
        }

        return new static($str);
    }

    /**
     * @return string
     */
    public function toBase64(): string
    {
        return base64_encode($this->data);
    }

    /**
     * @param array $bytes
     * @return static
     */
    public static function fromBinary(array $bytes): static
    {
        $bytes = implode(" ", $bytes);
        if (!preg_match('/^[01]{1,8}(\s[01]{1,8})*$/', $bytes)) {
            throw new \InvalidArgumentException('Cannot instantiate Buffer; expected Binary');
        }

        $bytes = explode(" ", $bytes);
        $bA = [];
        foreach ($bytes as $byte) {
            $bA[] = gmp_intval(gmp_init($byte, 2));
        }

        return static::fromByteArray($bA);
    }

    /**
     * @param bool $padded8bits
     * @return array
     */
    public function toBinary(bool $padded8bits = false): array
    {
        $bA = $this->toByteArray();
        $bin = [];
        foreach ($bA as $byte) {
            $bin[] = $padded8bits ? str_pad(decbin($byte), 8, "0", STR_PAD_LEFT) : decbin($byte);
        }

        return $bin;
    }

    /**
     * @param int $bytes
     * @return $this
     */
    public function checkSize(int $bytes): static
    {
        if ($this->len !== $bytes) {
            throw new \LengthException(sprintf('Expected value of %d bytes; got %d', $bytes, $this->len));
        }

        return $this;
    }

    /**
     * @return array
     */
    public function __debugInfo(): array
    {
        return [
            "size" => $this->len,
        ];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function __serialize(): array
    {
        return [
            $this->readOnly,
            $this->len,
            base64_encode($this->data)
        ];
    }

    /**
     * @param array $data
     */
    public function __unserialize(array $data)
    {
        $len = intval($data[1]);
        if ($len > 0) {
            $bytes = base64_decode($data[2], true);
            if ($bytes === false) {
                throw new \UnexpectedValueException('Could not decode base64 encoded data');
            }

            if (strlen($bytes) !== $len) {
                throw new \LengthException(
                    sprintf('Buffer serialized with len of %d cannot be reinstated with %d bytes', $len, strlen($bytes))
                );
            }

            $this->setBuffer($bytes);
        }

        $this->readOnly = intval($data[0]) === 1;
    }

    /**
     * @return int
     */
    public function len(): int
    {
        return $this->len;
    }

    /**
     * @return string
     */
    public function raw(): string
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function toByteArray(): array
    {
        $bA = [];
        for ($i = 0; $i < $this->len; $i++) {
            $bA[] = ord($this->data[$i]);
        }

        return $bA;
    }

    /**
     * Pops out given number of bytes from buffer.
     * - If positive number of bytes given in argument, it gets N number of bytes from start of buffer.
     * - If negative number of bytes given in argument, it gets N number of bytes from end of buffer.
     * - If second argument "changeBuffer" is set to TRUE, buffer state will be updated internally
     * otherwise no changes will be made to buffer.
     * @param int $bytes
     * @param bool $changeBuffer
     * @return string|false
     */
    public function pop(int $bytes, bool $changeBuffer = true): string|false
    {
        if ($bytes !== 0) {
            $result = $bytes > 0 ? substr($this->data, 0, $bytes) : substr($this->data, $bytes);
            if (strlen($result) === ($bytes > 0 ? $bytes : $bytes * -1)) {
                if ($changeBuffer) {
                    $this->setBuffer($bytes > 0 ? substr($this->data, $bytes) : substr($this->data, 0, $bytes));
                }

                return $result;
            }
        }

        return false;
    }

    /**
     * Creates a new Buffer from given arguments
     * @param int|null $start
     * @param int|null $length
     * @return $this
     */
    public function copy(?int $start = null, ?int $length = null): static
    {
        $bytes = $this->data;
        if (is_int($start)) {
            $bytes = is_int($length) ? substr($bytes, $start, $length) : substr($bytes, $start);
        }

        return new static($bytes);
    }

    /**
     * Compares 2 buffers
     * @param AbstractByteArray|string $cmp
     * @return bool
     */
    public function equals(AbstractByteArray|string $cmp): bool
    {
        if ($cmp instanceof AbstractByteArray) {
            $cmp = $cmp->raw();
        }

        return $this->len === strlen($cmp) && $this->data === $cmp;
    }

    /**
     * @return ByteReader
     */
    public function read(): ByteReader
    {
        return new ByteReader($this);
    }

    /**
     * @return ByteDigest
     */
    public function hash(): ByteDigest
    {
        return new ByteDigest($this);
    }

    /**
     * @return $this
     */
    public function switchEndianness(): static
    {
        return new static(ByteOrder::SwapEndianness($this->raw()));
    }

    /**
     * @return array
     */
    public function dump(): array
    {
        return [
            "bytes" => $this->toByteArray(),
            "size" => $this->len,
            "readOnly" => $this->readOnly,
        ];
    }

    /**
     * @param \Closure $func
     * @return $this
     */
    public function applyFn(\Closure $func): static
    {
        $applied = $func($this->data);
        if (!is_string($applied)) {
            throw new \UnexpectedValueException(sprintf('Expected string from apply callback, got "%s"', gettype($applied)));
        }

        return new static($applied);
    }
}
