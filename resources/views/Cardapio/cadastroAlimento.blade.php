<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
       <div class="fr-card-header">
          @foreach($submodulos as $s)
          <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
          @endforeach
       </div>
       <div class="fr-card-body">
        <form action="{{route('Merenda/Alimentos/Save')}}" method="POST">
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
            @if(isset($Registro))
            <input type="hidden" value="{{$id}}" name="id">
            @endif
            <div class="col-sm-12 p-2">
               {{-- <pre>
                  {{print_r($EscolasRegistradas)}}
               </pre> --}}
               <div class="row">
                <div class="col-sm-12">
                    <label>Escola</label>
                    <select name="IDEmpresa" class="form-control">
                        <option value="">Selecione</option>
                        @foreach($Escolas as $e)
                        <option value="{{$e->id}}" {{isset($Registro) && $Registro->IDEscola == $e->id ? 'selected' : ''}}>{{$e->Nome}}</option>
                        @endforeach
                    </select>
                </div>
               </div>
               <div class="row">
                   <div class="col-sm-4">
                     <label>Nome do Alimento</label>
                     <input type="text" class="form-control" name="Nome" value="{{isset($Registro) ? $Registro->Nome : ''}}">
                   </div>
                   <div class="col-sm-4">
                    <label>Grau de Dificuldade</label>
                    <input type="text" class="form-control" name="GDificuldade" value="{{isset($Registro) ? $Registro->GDificuldade : ''}}">
                  </div>
                  <div class="col-sm-4">
                    <label>Rendimento</label>
                    <input type="text" class="form-control" name="Rendimento" value="{{isset($Registro) ? $Registro->Rendimento : ''}}">
                  </div>
               </div>
                <div class="row">
                   <div class="col-sm-12">
                      <label>Modo de Preparo</label>
                      <textarea class="form-control" maxlength="250" name="MDPreparo" placeholder="Max 250 Caracteres" rows="3" required>{{isset($Registro) ? $Registro->MDPreparo : ''}}</textarea>
                   </div>
                </div>
                <hr>
                <h3>Tabela Nutricional</h3>
                <div class="row">
                    <div class="col-sm-1">
                        <label>Zinco</label>
                        <input name="Zinco" value="{{ isset($Nutrientes) ? $Nutrientes->Zinco : '' }}" class="form-control">
                    </div>
                    <div class="col-sm-1">
                        <label>Ferro</label>
                        <input name="Ferro" value="{{ isset($Nutrientes) ? $Nutrientes->Ferro : '' }}" class="form-control">
                    </div>
                    <div class="col-sm-1">
                        <label>Cálcio</label>
                        <input name="Calcio" value="{{ isset($Nutrientes) ? $Nutrientes->Calcio : '' }}" class="form-control">
                    </div>
                    <div class="col-sm-1">
                        <label>Magnésio</label>
                        <input name="Magnesio" value="{{ isset($Nutrientes) ? $Nutrientes->Magnesio : '' }}" class="form-control">
                    </div>
                    <div class="col-sm-1">
                        <label>Sódio</label>
                        <input name="Sodio" value="{{ isset($Nutrientes) ? $Nutrientes->Sodio : '' }}" class="form-control">
                    </div>
                    <div class="col-sm-1">
                        <label>Potássio</label>
                        <input name="Potassio" value="{{ isset($Nutrientes) ? $Nutrientes->Potassio : '' }}" class="form-control">
                    </div>
                    <div class="col-sm-1">
                        <label>Fósforo</label>
                        <input name="Fosforo" value="{{ isset($Nutrientes) ? $Nutrientes->Fosforo : '' }}" class="form-control">
                    </div>
                    <div class="col-sm-1">
                        <label>Vitamina A</label>
                        <input name="VitaminaA" value="{{ isset($Nutrientes) ? $Nutrientes->VitaminaA : '' }}" class="form-control">
                    </div>
                    <div class="col-sm-1">
                        <label>Vitamina C</label>
                        <input name="VitaminaC" value="{{ isset($Nutrientes) ? $Nutrientes->VitaminaC : '' }}" class="form-control">
                    </div>
                    <div class="col-sm-1">
                        <label>Vitamina D</label>
                        <input name="VitaminaD" value="{{ isset($Nutrientes) ? $Nutrientes->VitaminaD : '' }}" class="form-control">
                    </div>
                    <div class="col-sm-1">
                        <label>Vitamina E</label>
                        <input name="VitaminaE" value="{{ isset($Nutrientes) ? $Nutrientes->VitaminaE : '' }}" class="form-control">
                    </div>
                    <div class="col-sm-1">
                        <label>Vitamina K</label>
                        <input name="VitaminaK" value="{{ isset($Nutrientes) ? $Nutrientes->VitaminaK : '' }}" class="form-control">
                    </div>
                    <div class="col-sm-1">
                        <label>Proteína</label>
                        <input name="Proteina" value="{{ isset($Nutrientes) ? $Nutrientes->Proteina : '' }}" class="form-control">
                    </div>
                    <div class="col-sm-1">
                        <label>Carboidrato</label>
                        <input name="Carboidrato" value="{{ isset($Nutrientes) ? $Nutrientes->Carboidrato : '' }}" class="form-control">
                    </div>
                    <div class="col-sm-1">
                        <label>Gordura</label>
                        <input name="Gordura" value="{{ isset($Nutrientes) ? $Nutrientes->Gordura : '' }}" class="form-control">
                    </div>
                </div>
                <br>
                <div class="row">
                   <div class="col-auto">
                       <button class="btn btn-fr">Salvar</button>
                   </div>
                   <div class="col-auto">
                       <a class="btn btn-light" href="{{route('Merenda/Alimentos/index')}}">Cancelar</a>
                   </div>
                </div>
             </div>
        </form>
       </div>
    </div>
 </x-educacional-layout>