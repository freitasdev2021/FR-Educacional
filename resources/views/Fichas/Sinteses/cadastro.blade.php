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
                <form action="{{route('Fichas/Sinteses/Save')}}" method="POST">
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
                        <div class="col-sm-4">
                            <label>Referência</label>
                            <select name="Referencia" class="form-control">
                                <option value="">Selecione</option>
                                <option value="A" {{isset($Registro) && $Registro->Referencia == "A" ? 'selected' : ''}}>A</option>
                                <option value="B" {{isset($Registro) && $Registro->Referencia == "B" ? 'selected' : ''}}>B</option>
                                <option value="C" {{isset($Registro) && $Registro->Referencia == "C" ? 'selected' : ''}}>C</option>
                                <option value="D" {{isset($Registro) && $Registro->Referencia == "D" ? 'selected' : ''}}>D</option>
                                <option value="E" {{isset($Registro) && $Registro->Referencia == "E" ? 'selected' : ''}}>E</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label>Disciplina</label>
                            <select name="IDDisciplina" class="form-control">
                                <option value="">Selecione</option>
                                @foreach($Disciplinas as $d)
                                <option value="{{$d->IDDisciplina}}" {{isset($Registro->IDDisciplina) && $Registro->IDDisciplina == $d->IDDisciplina ? 'selected' : ''}}>{{$d->Disciplina}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label>Síntese</label>
                            <input type="text" name="Sintese" class="form-control" value="{{isset($Registro) ? $Registro->Sintese : ''}}">
                        </div>
                    </div>
                    <br>
                    <div class="col-sm-12 text-left row">
                        @if(in_array(Auth::user()->tipo,[4,4.5,6,5,6,5,5.5]))
                        <button type="submit" class="btn btn-fr col-auto">Salvar</button>
                        &nbsp;
                        @endif
                        <a class="btn btn-light col-auto" href="{{route('Fichas/Sinteses')}}">Voltar</a>
                    </div>
                </form>    
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>