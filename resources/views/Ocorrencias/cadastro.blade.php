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
                <form action="{{route('Ocorrencias/Save')}}" method="POST">
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
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Alvo</label>
                            <select name="IDAlvo" class="form-control">
                                <option value="">Selecione</option>
                                @foreach($Alvos as $a)
                                <option value="{{$a->id}}" {{isset($Registro) && $Registro->IDAlvo == $a->id ? 'selected' : ''}}>{{$a->Alvo}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label>Data e Hora</label>
                            <input type="datetime-local" name="DTOcorrencia" class="form-control" value="{{isset($Registro) ? $Registro->DTOcorrencia : ''}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Descrição do Ocorrido</label>
                            <textarea name="DSOcorrido" class="form-control">{{isset($Registro) ? $Registro->DSOcorrido : ''}}</textarea>
                        </div>
                    </div>
                    <br>
                    <div class="col-sm-12 text-left row">
                        <button type="submit" class="btn btn-fr col-auto">Salvar</button>
                        &nbsp;
                        <a class="btn btn-light col-auto" href="{{route('Ocorrencias/index')}}">Voltar</a>
                    </div>
                </form>    
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>