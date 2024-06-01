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
                <form action="{{route('Transporte/Save')}}" method="POST">
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
                    @if(isset($Registro->IDRota))
                    <input type="hidden" name="id" value="{{$Registro->IDRota}}">
                    @endif
                    <input type="hidden" name="Ramo" value="Transportes">
                    <div class="row">
                        <div class="col-sm-9">
                            <label>Motorista</label>
                            <select name="IDMotorista" class="form-control">
                                <option>Selecione</option>
                                @foreach($Motoristas as $m)
                                <option value="{{$m['id']}}" {{isset($Registro) && $Registro->IDMotorista == $m['id'] ? 'selected' : ''}}>{{$m['Nome']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Distancia Total</label>
                            <input class="form-control" name="Distancia" type="text" value="{{isset($Registro) ? $Registro->Distancia : ''}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Partida</label>
                            <input type="text" name="Partida" class="form-control" maxlength="50" required value="{{isset($Registro) ? $Registro->Partida : ''}}">
                        </div>
                        <div class="col-sm-6">
                            <label>Hora Partida</label>
                            <input type="time" name="HoraPartida" class="form-control" value="{{isset($Registro) ? $Registro->HoraPartida : ''}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Chegada</label>
                            <input type="text" name="Chegada" class="form-control" required value="{{isset($Registro) ? $Registro->Chegada : ''}}">
                        </div>
                        <div class="col-sm-6">
                            <label>Hora Chegada</label>
                            <input type="time" name="HoraChegada" class="form-control" required value="{{isset($Registro) ? $Registro->HoraChegada : ''}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Descrição</label>
                            <input type="text" name="Descricao" class="form-control" maxlength="50" value="{{isset($Registro) ? $Registro->Descricao : ''}}" minlength="3" required>
                        </div>
                        <div class="col-sm-6">
                            <label>Turno</label>
                            <select name="Turno" class="form-control" required>
                                <option value="">Selecione</option>
                                <option value="Manhã" {{isset($Registro) && $Registro->Turno == "Manhã" ? 'selected' : ''}}>Manhã</option>
                                <option value="Tarde" {{isset($Registro) && $Registro->Turno == "Tarde" ? 'selected' : ''}}>Tarde</option>
                                <option value="Noite" {{isset($Registro) && $Registro->Turno == "Noite" ? 'selected' : ''}}>Noite</option>
                            </select>
                        </div>
                    </div>
                    <label>Dias</label>
                    <div class="col-auto row">
                        <div class="form-check d-flex ">
                            <div class="col-sm-2">
                                <input class="form-check-input" type="checkbox" value="Segunda" {{isset($Dias) && in_array("Segunda",$Dias) ? 'checked' : ''}} name="Dia[]" id="flexCheckDefault">
                                <label class="form-check-label" for="flexCheckDefault">
                                  Segunda
                                </label>
                            </div>
                            <div class="col-sm-2">
                                <input class="form-check-input" type="checkbox" value="Terça" {{isset($Dias) && in_array("Terça",$Dias) ? 'checked' : ''}} name="Dia[]" id="flexCheckDefault">
                                <label class="form-check-label" for="flexCheckDefault">
                                  Terça
                                </label>
                            </div>
                            <div class="col-sm-2">
                                <input class="form-check-input" type="checkbox" value="Quarta" {{isset($Dias) && in_array("Quarta",$Dias) ? 'checked' : ''}} name="Dia[]" id="flexCheckDefault">
                                <label class="form-check-label" for="flexCheckDefault">
                                  Quarta
                                </label>
                            </div>
                            <div class="col-sm-2">
                                <input class="form-check-input" type="checkbox" value="Quinta" {{isset($Dias) && in_array("Quinta",$Dias) ? 'checked' : ''}} name="Dia[]"id="flexCheckDefault">
                                <label class="form-check-label" for="flexCheckDefault">
                                  Quinta
                                </label>
                            </div>
                            <div class="col-sm-2">
                                <input class="form-check-input" type="checkbox" value="Sexta" {{isset($Dias) && in_array("Sexta",$Dias) ? 'checked' : ''}} name="Dia[]" id="flexCheckDefault">
                                <label class="form-check-label" for="flexCheckDefault">
                                  Sexta
                                </label>
                            </div>
                            <div class="col-sm-2">
                                <input class="form-check-input" type="checkbox" value="Sabado" {{isset($Dias) && in_array("Sabado",$Dias) ? 'checked' : ''}} name="Dia[]" id="flexCheckDefault">
                                <label class="form-check-label" for="flexCheckDefault">
                                  Sabado
                                </label>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="col-sm-12 text-left row">
                        <button type="submit" class="btn btn-fr col-auto">Salvar</button>
                        &nbsp;
                        <a class="btn btn-light col-auto" href="{{route('Transporte/index')}}">Voltar</a>
                    </div>
                </form>    
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>