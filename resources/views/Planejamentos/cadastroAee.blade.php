<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
       <div class="fr-card-header">
          @foreach($submodulos as $s)
          <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
          @endforeach
       </div>
       <div class="fr-card-body">
          <!--CABECALHO-->
          <form action="{{route('Planejamentos/AEE/Save')}}" method="POST">
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
            @if(isset($id))
            <input type="hidden" name="id" value="{{$id}}">
            @endif
            <div class="row">
               <div class="col-sm-6">
                  <label>Nome</label>
                  <input type="text" name="Nome" class="form-control" value="{{isset($Registro) ? $Registro->Nome : ''}}">
               </div>
               <div class="col-sm-6">
                  <label>Turma</label>
                  <select name="IDTurma" class="form-control">
                     <option value="">Selecione</option>
                     @foreach($Turmas as $t)
                        <option value="{{$t->id}}" {{(isset($Registro) && $Registro->IDTurma == $t->id) ? 'selected' : ''}}>{{$t->Nome}} - {{$t->Serie}}</option>
                     @endforeach
                  </select>
               </div>
            </div>
            <br>
            <div class="col-sm-12 text-left row">
               <button type="submit" class="btn btn-fr col-auto">Salvar</button>
               &nbsp;
               <a class="btn btn-light col-auto" href="{{route('Planejamentos/AEE')}}">Voltar</a>
            </div>
          </form>
          <!--//-->
       </div>
    </div>
 </x-educacional-layout>