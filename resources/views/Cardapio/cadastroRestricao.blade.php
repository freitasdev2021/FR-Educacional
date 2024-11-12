<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
       <div class="fr-card-header">
          @foreach($submodulos as $s)
          <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
          @endforeach
       </div>
       <div class="fr-card-body">
        <form action="{{route('Merenda/Restricoes/Save')}}" method="POST">
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
                   <div class="col-sm-4">
                     <label>Aluno</label>
                     <select name="IDAluno" class="form-control" required>
                        <option value="">Selecione</option>
                        @foreach($Alunos as $a)
                        <option value="{{$a->id}}" {{isset($Registro) && $Registro->IDAluno == $a->id ? 'selected' : ''}}>{{$a->Nome}}</option>
                        @endforeach
                     </select>
                   </div>
                   <div class="col-sm-4">
                    <label>Restrição</label>
                    <input type="text" class="form-control" name="NMRestricao" value="{{isset($Registro) ? $Registro->NMRestricao : ''}}" required>
                  </div>
                  <div class="col-sm-4">
                    <label>Substituto</label>
                    <input type="text" class="form-control" name="Substituto" value="{{isset($Registro) ? $Registro->Substituto : ''}}" required>
                  </div>
               </div>
                <br>
                <div class="row">
                   <div class="col-auto">
                       <button class="btn btn-fr">Salvar</button>
                   </div>
                   <div class="col-auto">
                       <a class="btn btn-light" href="{{route('Merenda/Restricoes/index')}}">Cancelar</a>
                   </div>
                </div>
             </div>
        </form>
       </div>
    </div>
 </x-educacional-layout>