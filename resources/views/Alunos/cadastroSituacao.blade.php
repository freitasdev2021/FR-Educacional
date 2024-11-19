<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <form action="{{route('Alunos/Situacao/Save')}}" method="POST" enctype="multipart/form-data">
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
                <div class="col-sm-12 p-2">
                    <input type="hidden" value="{{$id}}" name="IDAluno">
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Nova Situação</label>
                            <select name="STAluno" class="form-control" required>
                                <option value="">Selecione</option>
                                <option value="0">Frequente</option>
                                <option value="1">Evadido</option>
                                <option value="2">Desistente</option>
                                <option value="3">Desligado</option>
                                <option value="4">Egresso</option>
                                <option value="5">Transferido Para Outra Rede</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label>Data da Situação</label>
                            <input type="date" class="form-control" name="DTSituacao">
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
                            <a class="btn btn-light" href="{{route('Alunos/Situacao',$id)}}">Cancelar</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-educacional-layout>