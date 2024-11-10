<x-educacional-layout>
    <x-educacional-layout>
        <div class="fr-card p-0 shadow col-sm-12">
            <div class="fr-card-header">
               @foreach($submodulos as $s)
                <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
               @endforeach
            </div>
            <div class="fr-card-body">
                <br>
                <div class="col-sm-12 row d-flex justify-content-center">
                    <div class="col-sm-2">
                        <label>Nome:</label>
                        <span>{{$Matricula->Nome}}</span>
                    </div>
                    <div class="col-sm-3">
                        <label>Endereço:</label>
                        <span>{{$Matricula->Rua.", ".$Matricula->Numero." ".$Matricula->Bairro." ".$Matricula->Cidade." - ".$Matricula->UF}}</span>
                    </div>
                    <div class="col-sm-2">
                        <label>Escola:</label>
                        <span>{{$Matricula->Escola}}</span>
                    </div>
                    <div class="col-sm-2">
                        <label>Turma:</label>
                        <span>{{$Matricula->Turma}}</span>
                    </div>
                    <div class="col-sm-2">
                        <label>Série:</label>
                        <span>{{$Matricula->Serie}}</span>
                    </div>
                </div>
                <hr>
                <h3 class="p-2" align="center">Matrículas Anteriores</h3>
                <div class="col-sm-12">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <td>Escola</td>
                                <td>Matrícula</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($Matriculas as $m)
                            <tr>
                                <td>{{$a->Escola}}</td>
                                <td>{{$a->DTMatricula}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </x-educacional-layout>
</x-educacional-layout>