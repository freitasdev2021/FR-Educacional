<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$IDProfessor)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--CABECALHO-->
            <div class="col-sm-12 p-2 row">
                <div class="col-auto">
                    <a href="{{route('Professores/Turnos/Novo',$IDProfessor)}}" class="btn btn-fr">Adicionar</a>
                </div>
            </div>
            <!--LISTAS-->
            <div class="col-sm-12 p-2">
                <hr>
                <table class="table table-sm tabela" id="escolas" data-rota="{{route('Professores/Turnos/list',$IDProfessor)}}">
                    <thead>
                      <tr>
                        @if(in_array(Auth::user()->tipo,[2,2.5]))<th style="text-align:center;" scope="col">Escola</th>@endif
                        <th style="text-align:center;" scope="col">Turma</th>
                        <th style="text-align:center;" scope="col">Disciplina</th>
                        <th style="text-align:center;" scope="col">Dia</th>
                        <th style="text-align:center;" scope="col">Inicio</th>
                        <th style="text-align:center;" scope="col">Termino</th>
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