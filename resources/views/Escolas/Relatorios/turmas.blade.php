<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <form action="{{route('Escolas/Relatorios/Gerar',$Tipo)}}" method="POST">
                @csrf
                @method('PUT')
                <h5>Escolha os dados do relatório (Para caber na Tela em modo Retrato com Perfeição, Escolha no Máximo 8 Colunas)</h5>
                <div class="col-sm-12 p-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="INEP" name="Conteudo[]" id="flexCheckDefault">
                        <label class="form-check-label" for="flexCheckDefault">
                          INEP
                        </label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="N°" name="Conteudo[]" id="flexCheckChecked">
                        <label class="form-check-label" for="flexCheckChecked">
                          Código
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Nome" name="Conteudo[]" id="flexCheckChecked">
                        <label class="form-check-label" for="flexCheckChecked">
                          Nome
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Filiação" name="Conteudo[]" id="flexCheckChecked">
                        <label class="form-check-label" for="flexCheckChecked">
                          Filiação
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Nascimento" name="Conteudo[]" id="flexCheckChecked">
                        <label class="form-check-label" for="flexCheckChecked">
                          Nascimento
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Celular" name="Conteudo[]" id="flexCheckChecked">
                        <label class="form-check-label" for="flexCheckChecked">
                          Celular
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Sexo" name="Conteudo[]" id="flexCheckChecked">
                        <label class="form-check-label" for="flexCheckChecked">
                          Sexo
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Idade" name="Conteudo[]" id="flexCheckChecked">
                        <label class="form-check-label" for="flexCheckChecked">
                          Idade
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Naturalidade" name="Conteudo[]" id="flexCheckChecked">
                        <label class="form-check-label" for="flexCheckChecked">
                          Naturalidade
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Tipo Sanguíneo" name="Conteudo[]" id="flexCheckChecked">
                        <label class="form-check-label" for="flexCheckChecked">
                          Tipo Sanguíneo
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Endereço" name="Conteudo[]" id="flexCheckChecked">
                        <label class="form-check-label" for="flexCheckChecked">
                          Endereço
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="RG" name="Conteudo[]" id="flexCheckChecked">
                        <label class="form-check-label" for="flexCheckChecked">
                          RG
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="CPF" name="Conteudo[]" id="flexCheckChecked">
                        <label class="form-check-label" for="flexCheckChecked">
                          CPF
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Cartão SUS" name="Conteudo[]" id="flexCheckChecked">
                        <label class="form-check-label" for="flexCheckChecked">
                          Cartão SUS
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="NIS" name="Conteudo[]" id="flexCheckChecked">
                        <label class="form-check-label" for="flexCheckChecked">
                          NIS
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Data da Matrícula" name="Conteudo[]" id="flexCheckChecked">
                        <label class="form-check-label" for="flexCheckChecked">
                          Data da Matrícula
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Situação da Matrícula" name="Conteudo[]" id="flexCheckChecked">
                        <label class="form-check-label" for="flexCheckChecked">
                          Situação da Matrícula
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Bolsa Família" name="Conteudo[]" id="flexCheckChecked">
                        <label class="form-check-label" for="flexCheckChecked">
                          Bolsa Família
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Raça" name="Conteudo[]" id="flexCheckChecked">
                        <label class="form-check-label" for="flexCheckChecked">
                          Raça
                        </label>
                    </div>
                </div>
                <div class="row col-sm-12">
                    <div class="col-sm-4">
                        <label>Tipo do Arquivo</label>
                        <select name="TPArquivo" class="form-control">
                            <option value="PDF">PDF</option>
                            <option value="XLSX">XLSX</option>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label>Formato</label>
                        <select name="TPRelatorio" class="form-control">
                            <option value="Retrato">Retrato</option>
                            <option value="Paisagem">Paisagem</option>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label>Tamanho da Fonte</label>
                        <input type="number" class="form-control" name="TMFonte">
                    </div>
                </div>
                <hr>
                <button class="btn btn-primary col-auto">Gerar</button>
                <a href="{{route('Escolas/Relatorios')}}" class="btn btn-light col-auto">Voltar</a>
            </form>
        </div>
    </div>
</x-educacional-layout>