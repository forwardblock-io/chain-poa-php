<?php
declare(strict_types=1);

namespace ForwardBlock\Chain\PoA;

use Comely\DataTypes\Buffer\Base16;
use ForwardBlock\Protocol\AbstractProtocolChain;
use ForwardBlock\Protocol\Accounts\ChainAccountInterface;
use ForwardBlock\Protocol\KeyPair\PublicKey;
use FurqanSiddiqui\BIP32\Exception\PublicKeyException;

/**
 * Class ChainMaster
 * @package ForwardBlock\Chain\PoA
 */
class ChainMaster extends PublicKey implements ChainAccountInterface
{
    /** @var string ChainMaster Account Public Key */
    public const PUBLIC_KEY = "03053c689577b88cfc61279963e17d83025028b400b38ccaa0b14536733205566b";
    /** @var string ChainMaster Signatory # 1 */
    public const MULTI_SIG_KEY_1 = "03053c689577b88cfc61279963e17d83025028b400b38ccaa0b14536733205566b";
    /** @var string ChainMaster Signatory # 2 */
    public const MULTI_SIG_KEY_2 = "03a41332c77db97752251c013e34400e93e88a65cba09c4cc67bc01a59598775f9";
    /** @var string ChainMaster Signatory # 3 */
    public const MULTI_SIG_KEY_3 = "0201c8a43bf301da7be4c3e71d7914a3af2c31911e3a86a3026f45b07063170569";
    /** @var string ChainMaster Signatory # 4 */
    public const MULTI_SIG_KEY_4 = "036b61e1a693d0a2892a02d1acb637a13a9d77badf936648688bcd2e5bb1c366e0";
    /** @var string ChainMaster Signatory # 5 */
    public const MULTI_SIG_KEY_5 = "0213f57c39a0abc589134ac5bf16225168a2b76de267a896f9a70b2571c5534dd8";
    /** @var int Initial Supply 100,000,000 */
    public const INITIAL_SUPPLY = 10000000000000000;

    /** @var array */
    private array $multiSigPubs = [];

    /**
     * ChainMaster constructor.
     * @param AbstractProtocolChain $p
     * @throws \FurqanSiddiqui\BIP32\Exception\PublicKeyException
     */
    public function __construct(AbstractProtocolChain $p)
    {
        parent::__construct($p, null, $p->secp256k1(), new Base16(static::PUBLIC_KEY), true);
    }

    /**
     * @return int
     */
    public function initialSupply(): int
    {
        return self::INITIAL_SUPPLY;
    }

    /**
     * @return bool
     */
    public function canForgeBlocks(): bool
    {
        return true;
    }

    /**
     * @return array
     * @throws PublicKeyException
     */
    public function getAllPublicKeys(): array
    {
        $pubKeys = [];
        for ($i = 1; $i <= 5; $i++) {
            $pubKeys[] = $this->getSignatory($i);
        }

        return $pubKeys;
    }

    /**
     * @return PublicKey
     * @throws \FurqanSiddiqui\BIP32\Exception\PublicKeyException
     */
    public function getMultiSig1(): PublicKey
    {
        return $this->getSignatory(1);
    }

    /**
     * @return PublicKey
     * @throws \FurqanSiddiqui\BIP32\Exception\PublicKeyException
     */
    public function getMultiSig2(): PublicKey
    {
        return $this->getSignatory(2);
    }

    /**
     * @return PublicKey
     * @throws \FurqanSiddiqui\BIP32\Exception\PublicKeyException
     */
    public function getMultiSig3(): PublicKey
    {
        return $this->getSignatory(3);
    }

    /**
     * @return PublicKey
     * @throws \FurqanSiddiqui\BIP32\Exception\PublicKeyException
     */
    public function getMultiSig4(): PublicKey
    {
        return $this->getSignatory(4);
    }

    /**
     * @return PublicKey
     * @throws \FurqanSiddiqui\BIP32\Exception\PublicKeyException
     */
    public function getMultiSig5(): PublicKey
    {
        return $this->getSignatory(5);
    }

    /**
     * @param int $num
     * @return PublicKey
     * @throws \FurqanSiddiqui\BIP32\Exception\PublicKeyException
     */
    private function getSignatory(int $num): PublicKey
    {
        if (!isset($this->multiSigPubs[$num])) {
            $this->multiSigPubs[$num] = new PublicKey($this->protocol, null, $this->protocol->secp256k1(), new Base16(constant("static::MULTI_SIG_KEY_" . $num)));
        }

        return $this->multiSigPubs[$num];
    }
}
