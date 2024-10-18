<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
       <div class="fr-card-header">
          @foreach($submodulos as $s)
          <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
          @endforeach
       </div>
       <div class="fr-card-body">
          <form action="{{route('Calendario/Periodos/Save')}}" method="POST">
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
             @if(isset($Registro->id))
             <input type="hidden" value="{{$Registro->id}}" name="id">
             @endif
             <div class="col-sm-12 p-2">
                <div class="row">
                    <div class="col-sm-12">
                        <label>Escola</label>
                        <select class="form-control" name="IDEscola" required>
                           <option>Selecione</option>
                           @foreach($Escolas as $es)
                           <option value="{{$es->id}}" {{(isset($Registro) && $Registro->IDEscola == $es->id) ? 'selected' : ''}}>{{$es->Nome}}</option>
                           @endforeach
                        </select>
                     </div>
                </div>
                <div class="row">
                   <div class="col-sm-6">
                      <label>De</label>
                      <input type="date" class="form-control" name="DTInicio" value="{{isset($Registro) ? $Registro->DTInicio : ''}}" required>
                   </div>
                   <div class="col-sm-6">
                      <label>Até</label>
                      <input type="date" class="form-control" name="DTTermino" value="{{isset($Registro) ? $Registro->DTTermino : ''}}" required>
                   </div>
                </div>
                <div class="row">
                  <div class="col-sm-12">
                     <label>Periodo</label>
                     <select class="form-control" name="Periodo">
                        <option value="">Selecione</option>
                        <!-- Bimestres -->
                        <option value="1° Bimestre" {{isset($Registro) && $Registro->Periodo == "1° Bimestre" ? 'selected' : ''}}>1° Bimestre</option>
                        <option value="2° Bimestre" {{isset($Registro) && $Registro->Periodo == "2° Bimestre" ? 'selected' : ''}}>2° Bimestre</option>
                        <option value="3° Bimestre" {{isset($Registro) && $Registro->Periodo == "3° Bimestre" ? 'selected' : ''}}>3° Bimestre</option>
                        <option value="4° Bimestre" {{isset($Registro) && $Registro->Periodo == "4° Bimestre" ? 'selected' : ''}}>4° Bimestre</option>
                        <!-- Trimestres -->
                        <option value="1° Trimestre" {{isset($Registro) && $Registro->Periodo == "1° Trimestre" ? 'selected' : ''}}>1° Trimestre</option>
                        <option value="2° Trimestre" {{isset($Registro) && $Registro->Periodo == "2° Trimestre" ? 'selected' : ''}}>2° Trimestre</option>
                        <option value="3° Trimestre" {{isset($Registro) && $Registro->Periodo == "3° Trimestre" ? 'selected' : ''}}>3° Trimestre</option>
                        <!-- Semestres -->
                        <option value="1° Semestre" {{isset($Registro) && $Registro->Periodo == "1° Semestre" ? 'selected' : ''}}>1° Semestre</option>
                        <option value="2° Semestre" {{isset($Registro) && $Registro->Periodo == "2° Semestre" ? 'selected' : ''}}>2° Semestre</option>
                        <!-- Período Anual -->
                        <option value="1° Periodo" {{isset($Registro) && $Registro->Periodo == "1° Periodo" ? 'selected' : ''}}>1° Periodo</option>
                    </select>                    
                  </div>
                </div>
                <br>
                <div class="row">
                   <div class="col-auto">
                      <button class="btn btn-fr">Salvar</button>
                   </div>
                   <div class="col-auto">
                      <a class="btn btn-light" href="{{route('Calendario/Periodos')}}">Cancelar</a>
                   </div>
                </div>
             </div>
          </form>
       </div>
    </div>
 </x-educacional-layout>