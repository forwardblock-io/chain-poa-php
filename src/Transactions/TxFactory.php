<?php
declare(strict_types=1);

namespace ForwardBlock\Chain\PoA\Transactions;

use Comely\DataTypes\Buffer\Binary;
use ForwardBlock\Chain\PoA\Transactions\Flags\AccountUpgradeTxConstructor;
use ForwardBlock\Chain\PoA\Transactions\Flags\RegisterTxConstructor;
use ForwardBlock\Protocol\AbstractProtocolChain;
use ForwardBlock\Protocol\KeyPair\PublicKey;
use ForwardBlock\Protocol\Transactions\AbstractPreparedTx;
use ForwardBlock\Protocol\Transactions\Transaction;

/**
 * Class TxFactory
 * @package ForwardBlock\Chain\PoA\Transactions
 */
class TxFactory implements TxFlagsInterface
{
    /** @var AbstractProtocolChain */
    private AbstractProtocolChain $p;

    /**
     * TxFactory constructor.
     * @param AbstractProtocolChain $p
     */
    public function __construct(AbstractProtocolChain $p)
    {
        $this->p = $p;
    }

    /**
     * @param PublicKey $publicKey
     * @param int|null $epoch
     * @return RegisterTxConstructor
     */
    public function registerTx(PublicKey $publicKey, ?int $epoch = null): RegisterTxConstructor
    {
        /** @var RegisterTxConstructor $tx */
        $tx = $this->createTx(self::TX_FLAG_REGISTER, [$this->p, $publicKey, $this->getEpochArg($epoch)]);
        return $tx;
    }

    /**
     * @param int $action
     * @param int|null $epoch
     * @return AccountUpgradeTxConstructor
     */
    public function accountUpgradeTx(int $action, ?int $epoch = null): AccountUpgradeTxConstructor
    {
        /** @var AccountUpgradeTxConstructor $tx */
        $tx = $this->createTx(self::TX_FLAG_ACCOUNT_UPGRADE, [$this->p, $this->getEpochArg($epoch), $action]);
        return $tx;
    }

    /**
     * @param Binary $encoded
     * @return AbstractPreparedTx
     * @throws \ForwardBlock\Protocol\Exception\TxDecodeException
     * @throws \ForwardBlock\Protocol\Exception\TxFlagException
     */
    public function decode(Binary $encoded): AbstractPreparedTx
    {
        return Transaction::DecodeAs($this->p, $encoded);
    }

    /**
     * @param int|null $epoch
     * @return int
     */
    private function getEpochArg(?int $epoch = null): int
    {
        if (is_null($epoch) || $epoch <= 0) {
            return time();
        }

        return $epoch;
    }

    /**
     * @param int $flag
     * @param array $args
     * @return ProtocolTxConstructor
     */
    private function createTx(int $flag, array $args): ProtocolTxConstructor
    {
        try {
            /** @var ProtocolTxConstructor $pTx */
            $pTx = $this->p->txFlags()->get($flag)->create($args);
        } catch (\Exception $e) {
            throw new \UnexpectedValueException(sprintf('[%s] %s', get_class($e), $e->getMessage()));
        }

        return $pTx;
    }
}
