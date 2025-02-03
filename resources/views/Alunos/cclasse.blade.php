<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$IDAluno)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <form action="{{route('Alunos/CClasse/Save',$IDAluno)}}" method="POST">
                @csrf
                @method("PATCH")
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Disciplina</th>
                            <th>Nota</th>
                            <th>Resultado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($Disciplinas as $d)
                        <tr>
                            <td>
                                <input type="hidden" value="{{!is_null($d->IDDisciplina) ? $d->IDDisciplina : '-'}}" name="Disciplina[]">
                                {{$d->NMDisciplina}}
                            </td>
                            <td><input type="text" name="Nota[]" value="{{!is_null($d->Nota) ? $d->Nota : 0}}"></td>
                            <td><input type="text" name="Situacao[]" value="{{!is_null($d->Situacao) ? $d->Situacao : '-'}}"></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <br>
                <div class="col-auto">
                    <button class="btn btn-fr">Salvar</button>
                </div>
            </form>            
        </div>
    </div>
</x-educacional-layout>