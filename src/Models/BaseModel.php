<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter Shield.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace CodeIgniter\Shield\Models;

use CodeIgniter\Model;
use CodeIgniter\Shield\Config\Auth;
use Ramsey\Uuid\Uuid;

abstract class BaseModel extends Model
{
    use CheckQueryReturnTrait;

    /**
     * Auth Table names
     */
    protected array $tables;
    protected $useAutoIncrement = false;
    protected $beforeInsert = ['setPrimaryKey'];

    protected Auth $authConfig;

    public function __construct()
    {
        $this->authConfig = config('Auth');

        if ($this->authConfig->DBGroup !== null) {
            $this->DBGroup = $this->authConfig->DBGroup;
        }

        parent::__construct();
    }

    protected function initialize(): void
    {
        $this->tables = $this->authConfig->tables;
    }

    protected function setPrimaryKey(array|object $data)
    {
        $newUuid = Uuid::uuid4()->toString();

        if (is_object($data)) {
            if ($data->data->{$this->primaryKey} == '')
            {
                $data->data->{$this->primaryKey} == $newUuid;
            }
        } else {
            if ($this->useAutoIncrement == false && !isset($data['data'][$this->primaryKey])) {
                $data['data'][$this->primaryKey] = $newUuid;
            }
        }

        return $data;
    }

    protected function getLastId()
    {
        $builder = $this->db->table($this->table);
        $lastRow = $builder->select($this->primaryKey)->orderBy($this->createdField, 'DESC')->limit(1)->get()->getRow();

        return $lastRow->id;
    }
}
