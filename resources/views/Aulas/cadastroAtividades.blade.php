<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--LISTAS-->
            <div class="col-sm-12 p-2 center-form">
                <form action="{{route('Professores/Save')}}" method="POST">
                    @csrf
                    @method("POST")
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
                    @if(isset($Registro->id))
                    <input type="hidden" name="id" value="{{$Registro->id}}">
                    @endif
                    <input type="hidden" name="IDOrg" value="{{Auth::user()->id_org}}">
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Aula da Atividade</label>
                            <select class="form-control" name="IDAula">
                                <option value="">Selecione</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label>Data de Entrega</label>
                            <input type="datetime-local" class="form-control" name="DTEntrega">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-10">
                            <label>Conteudo</label>
                            <input type="text" class="form-control" name="TPConteudo">
                        </div>
                        <div class="col-sm-2">
                            <label>Pontuação</label>
                            <input type="number" class="form-control" name="Pontuacao">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Descrição da Atividade</label>
                            <textarea name="DSAtividade" class="form-control"></textarea>
                        </div>
                    </div>
                    <br>
                    <div class="col-sm-12 text-left row">
                        <button type="submit" class="btn btn-fr col-auto">Salvar</button>
                        &nbsp;
                        <a class="btn btn-light col-auto" href="{{route('Aulas/Atividades/index')}}">Voltar</a>
                    </div>
                </form>    
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>