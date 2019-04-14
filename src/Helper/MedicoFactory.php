<?php

namespace App\Helper;

use App\Entity\Medico;
use App\Repository\EspecialidadeRepository;

class MedicoFactory implements EntidadeFactory
{
    /**
     * @var EspecialidadeRepository
     */
    private $especialidadeRepository;

    public function __construct(EspecialidadeRepository $especialidadeRepository)
    {
        $this->especialidadeRepository = $especialidadeRepository;
    }

    public function criarEntidade(string $json) : Medico
    {
        $dadosJson = json_decode($json);
        $especialidadeId = $dadosJson->especialidadeId;
        $especialidade = $this->especialidadeRepository->find($especialidadeId);

        $medico = new Medico();
        $medico->setCrm($dadosJson->crm);
        $medico->setNome($dadosJson->nome);
        $medico->setEspecialidade($especialidade);

        return $medico;
    }
}
