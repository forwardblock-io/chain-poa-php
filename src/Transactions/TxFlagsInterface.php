<?php
declare(strict_types=1);

namespace ForwardBlock\Chain\PoA\Transactions;

/**
 * Interface TxFlagsInterface
 * @package ForwardBlock\Chain\PoA\Transactions
 */
interface TxFlagsInterface
{
    /** @var int Genesis/Chain Initializer Transaction */
    public const TX_FLAG_GENESIS = 0x01;
    /** @var int Account upgrade op (chain master only) */
    public const TX_FLAG_ACCOUNT_UPGRADE = 0x11;
    /** @var int Account Registration Transaction */
    public const TX_FLAG_REGISTER = 0x64;
    /** @var int Account to Account transfer op */
    public const TX_FLAG_TRANSFER = 0xc8;
    /** @var int Create a new asset flag */
    public const TX_FLAG_ASSET_CREATE = 1101;
    /** @var int Pause an asset (asset owner only) */
    public const TX_FLAG_ASSET_PAUSE = 1102;
    /** @var int Disable an asset (chain master only) */
    public const TX_FLAG_ASSET_DISABLE = 1103;
}
