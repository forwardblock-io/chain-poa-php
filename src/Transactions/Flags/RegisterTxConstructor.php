<?php
declare(strict_types=1);

namespace ForwardBlock\Chain\PoA\Transactions\Flags;

use Comely\DataTypes\Buffer\Binary;
use ForwardBlock\Chain\PoA\ForwardPoA;
use ForwardBlock\Chain\PoA\Transactions\ProtocolTxConstructor;
use ForwardBlock\Protocol\Accounts\ChainAccountInterface;
use ForwardBlock\Protocol\Exception\TxConstructException;
use ForwardBlock\Protocol\Exception\TxEncodeException;
use ForwardBlock\Protocol\KeyPair\PrivateKey\Signature;
use ForwardBlock\Protocol\KeyPair\PublicKey;
use ForwardBlock\Protocol\Math\UInts;

/**
 * Class RegisterTxConstructor
 * @package ForwardBlock\Chain\PoA\Transactions\Flags
 */
class RegisterTxConstructor extends ProtocolTxConstructor
{
    /** @var PublicKey */
    private PublicKey $pubKey;
    /** @var ChainAccountInterface */
    private ChainAccountInterface $referrer;
    /** @var Signature */
    private Signature $regSign;
    /** @var array */
    private array $multiSig = [];

    /**
     * RegisterTx constructor.
     * @param ForwardPoA $p
     * @param PublicKey $new
     * @param int $epoch
     * @throws TxConstructException
     */
    public function __construct(ForwardPoA $p, PublicKey $new, int $epoch)
    {
        parent::__construct($p, 1, $p->txFlags()->getWithName("register"), $epoch);
        $this->pubKey = $new;
    }

    /**
     * @param ChainAccountInterface $referrer
     * @param Signature $registrantSignature
     * @return $this
     */
    public function setReferrer(ChainAccountInterface $referrer, Signature $registrantSignature): self
    {
        $this->referrer = $referrer;
        $this->regSign = $registrantSignature;
        return $this;
    }

    /**
     * @param PublicKey ...$keys
     * @return $this
     * @throws TxConstructException
     */
    public function setMultiSigKeys(PublicKey ...$keys): self
    {
        if (count($keys) > 4) {
            throw TxConstructException::Prop("account.multiSig", "Cannot add more than 4 public keys");
        }

        $this->multiSig[] = $this->pubKey;
        foreach ($keys as $key) {
            $this->multiSig[] = $key;
        }

        return $this;
    }

    /**
     * @throws TxEncodeException
     */
    protected function beforeSerialize(): void
    {
        $data = new Binary();

        if (!isset($this->pubKey)) {
            throw new TxEncodeException('Registrant public key is not set');
        }

        // Append new account's public key
        $pubPad = str_pad($this->pubKey->compressed()->binary()->raw(), 33, "\0", STR_PAD_LEFT);
        $data->append($pubPad);

        // Append referrer's public key
        if (!isset($this->referrer)) {
            throw new TxEncodeException('No referrer defined');
        }

        $data->append(hex2bin($this->referrer->getHash160())); // 20 bytes referrer

        // MultiSig?
        $multiSigCount = count($this->multiSig);
        $data->append(UInts::Encode_UInt1LE($multiSigCount));
        if ($multiSigCount) {
            /** @var PublicKey $pubKey */
            foreach ($this->multiSig as $pubKey) {
                $data->append(str_pad($pubKey->compressed()->binary()->raw(), 33, "\0", STR_PAD_LEFT));
            }
        }

        // Registrant's Signature
        if (!isset($this->regSign)) {
            throw new TxEncodeException('Registrant signature not set');
        }

        $data->append(hex2bin($this->regSign->r()->hexits(false)));
        $data->append(hex2bin($this->regSign->s()->hexits(false)));
        $data->append(UInts::Encode_UInt1LE($this->regSign->v()));

        $this->data = $data->readOnly(true);
    }
}
