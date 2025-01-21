<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-Submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--CABECALHO-->
            <div class="col-sm-12 p-2 row">
                <div class="col-auto">
                    <a href="{{route('Fichas/Sinteses/Novo')}}" class="btn btn-fr">Adicionar</a>
                </div>
                <form class="row col-auto" method="GET">
                    <div class="col-sm-8">
                        <select name="IDDisciplina" class="form-control">
                            <option value="">Filtre pela Disciplina</option>
                            @foreach($Disciplinas as $d)
                            <option value="{{$d->IDDisciplina}}" {{isset($_GET['IDDisciplina']) && $_GET['IDDisciplina'] == $d->IDDisciplina ? 'selected' : ''}}>{{$d->Disciplina}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <input type="submit" class="btn btn-light form-control" value="Filtrar">
                    </div>
                </form>
            </div>
            <hr>
            <!--LISTAS-->
            <div class="col-sm-12 p-2">
                <table class="table table-sm tabela" id="escolas" data-rota="{{route('Fichas/Sinteses/list',$AND)}}">
                    <thead>
                      <tr>
                        <th style="text-align:center;" scope="col">Referência</th>
                        <th style="text-align:center;" scope="col">Síntese</th>
                        <th style="text-align:center;" scope="col">Disciplina</th>
                        <th style="text-align:center;" scope="col">Opções</th>
                      </tr>
                    </thead>
                    <tbody>
                      
                    </tbody>
                  </table>
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>