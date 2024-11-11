<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <form action="{{route('EAD/Cursos/Save')}}" method="POST">
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
                @if(isset($Registro))
                <input type="hidden" name="id" value="{{$Registro->id}}">
                @endif
                <div class="col-sm-12">
                    <label>Nome do Curso</label>
                    <input type="text" name="NMCurso" class="form-control" value="{{isset($Registro) ? $Registro->NMCurso : ''}}">
                </div>
                <div class="col-sm-12">
                    <label>Instituição</label>
                    <select name="IDInstituicao" class="form-control">
                        <option value="">Instituição</option>
                        @foreach($Instituicoes as $i)
                        <option value="{{$i->id}}" {{isset($Registro) && $Registro->IDInstituicao == $i->id ? 'selected' : ''}}>{{$i->Nome}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-12">
                    <label>Descrição do Curso</label>
                    <textarea name="DSCurso" class="form-control">{{isset($Registro) ? $Registro->DSCurso : ''}}</textarea>
                </div>
                <br>
                <div class="col-sm-4">
                    <button class="btn btn-fr">Salvar</button>
                    <a class="btn btn-light" href="{{route('EAD/Cursos')}}">Voltar</a>
                </div>
            </form>
        </div>
    </div>
</x-educacional-layout>