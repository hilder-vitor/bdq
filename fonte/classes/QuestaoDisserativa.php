<?php

namespace classes_;


class QuestaoDisserativa extends Questao{
    
    public function getTipo() {
        return Questao::QUESTAO_DISSERTATIVA;
    }

}
