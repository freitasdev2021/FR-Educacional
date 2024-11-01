<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <div class="col-sm-12 p-2 row">
                <div class="col-auto">
                    <a href="{{route('Alunos/NEE/Novo',$id)}}" class="btn btn-fr">Adicionar</a>
                </div>
            </div>
            <hr>
            <table class="table table-sm tabela" id="escolas" data-rota="{{route('Alunos/NEE/list',$id)}}">
                <thead>
                  <tr>
                    <th style="text-align:center;" scope="col">Necessidade</th>
                    <th style="text-align:center;" scope="col">CID</th>
                    <th style="text-align:center;" scope="col">Data do Laudo</th>
                    <th style="text-align:center;" scope="col">Opções</th>
                  </tr>
                </thead>
                <tbody>
                  
                </tbody>
            </table>
        </div>
    </div>
</x-educacional-layout>