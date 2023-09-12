<?php
namespace Kafoso\DoctrineFirebirdDriver\Driver\FirebirdInterbase;

use Doctrine\DBAL\Driver\PDOConnection;

class Connection extends PDOConnection
{
    /** @var int */
    private int $previousAutocommitValue;

    /**
     * {@inheritdoc}
     *
     * Apparently, pdo_firebird transactions fail unless we explicitly change PDO::ATTR_AUTOCOMMIT ourselves.
     * @see https://stackoverflow.com/a/41749323/25804
     */
    public function beginTransaction(): bool
    {
        $this->previousAutocommitValue = $this->getAttribute(\PDO::ATTR_AUTOCOMMIT);
        $this->setAttribute(\PDO::ATTR_AUTOCOMMIT, 0);
        return parent::beginTransaction();
    }

    /**
     * {@inheritdoc}
     *
     * Apparently, pdo_firebird transactions fail unless we explicitly change PDO::ATTR_AUTOCOMMIT ourselves.
     * @see https://stackoverflow.com/a/41749323/25804
     */
    public function commit(): bool
    {
        $result = parent::commit();
        $this->resetAutocommitValue();
        return $result;
    }

    /**
     * {@inheritdoc)
     *
     * Apparently, pdo_firebird transactions fail unless we explicitly change PDO::ATTR_AUTOCOMMIT ourselves.
     * @see https://stackoverflow.com/a/41749323/25804
     */
    public function rollBack(): bool
    {
        $result = parent::rollBack();
        $this->resetAutocommitValue();
        return $result;
    }

    private function resetAutocommitValue(): void
    {
        if(isset($this->previousAutocommitValue)) {
            $this->setAttribute(\PDO::ATTR_AUTOCOMMIT, $this->previousAutocommitValue);
            unset($this->previousAutocommitValue);
        }
    }
}
