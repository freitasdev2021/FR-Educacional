<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <form action="{{route('Alunos/Transferencias/Save')}}" method="POST">
                @csrf
                @if(session('success'))
                <div class="col-sm-12 shadow p-2 bg-success text-white">
                   <strong>{{session('success')}}</strong>
                </div>
                @elseif(session('error'))
                <div class="col-sm-12 shadow p-2 bg-danger text-white">
                   <strong>{{session('error')}}</strong>
                </div>
                <br>
                @endif
                <div class="col-sm-12 shadow p-3 bg-warning text-white">
                   <strong>Ao Transferir o Aluno, a Matricula na nova Escola só será Concluida Perante Aprovação da Escola de Destino, Caso for da Mesma Rede</strong>
                </div>
                <br>
                <div class="col-sm-12 p-2">
                    <input type="hidden" value="{{$id}}" name="IDAluno">
                    <div class="row">
                        <div class="col-sm-4">
                            <input type="hidden" value="{{$IDEscola}}" name="IDEscolaOrigem">
                            <label>Escola de Destino</label>
                            <select name="IDEscolaDestino" class="form-control" required>
                                <option value="0">Escola Fora da Rede</option>
                                @foreach($Escolas as $e)
                                <option value="{{$e->id}}">{{$e->Nome." (".$e->QTVagas." Vagas Disponiveis)"}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label>Data de Transferência</label>
                            <input type="date" class="form-control" name="DTTransferencia">
                        </div>
                        <div class="col-sm-4">
                            <label>Cidade de Destino</label>
                            <input type="text" class="form-control" name="CDDestino">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Justificativa</label>
                            <textarea class="form-control" name="Justificativa" required></textarea> 
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-auto">
                            <button class="btn btn-fr">Salvar</button>
                        </div>
                        <div class="col-auto">
                            <a class="btn btn-light" href="{{route('Alunos/Transferencias',$id)}}">Cancelar</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-educacional-layout>