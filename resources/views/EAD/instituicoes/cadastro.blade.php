<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <form action="{{route('EAD/Instituicoes/Save')}}" method="POST">
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
                    <label>Nome da Instituição</label>
                    <input type="text" name="Nome" class="form-control" value="{{isset($Registro) ? $Registro->Nome : ''}}">
                </div>
                <div class="col-sm-12">
                    <label>Escola</label>
                    <select name="IDEscola" class="form-control">
                        <option value="">Escola</option>
                        @foreach($Escolas as $e)
                        <option value="{{$e->id}}" {{isset($Registro) && $Registro->IDEscola == $e->id ? 'selected' : ''}}>{{$e->Nome}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-12">
                    <label>Descrição da Instituição</label>
                    <textarea name="DSInstituicao" class="form-control">{{isset($Registro) ? $Registro->DSInstituicao : ''}}</textarea>
                </div>
                <br>
                <div class="col-sm-4">
                    <button class="btn btn-fr">Salvar</button>
                    <a class="btn btn-light" href="{{route('EAD/Instituicoes')}}">Voltar</a>
                </div>
            </form>
        </div>
    </div>
</x-educacional-layout>