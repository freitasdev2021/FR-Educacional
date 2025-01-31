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
                <form action="{{route('Biblioteca/Emprestimos/Save')}}" method="POST">
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
                    @if(!empty($id))
                    <input type="hidden" name="id" value="{{$id}}">
                    @endif
                    <div class="row">
                        <div class="col-sm-{{isset($Registro) ? '8' : '4'}}">
                            <label>Leitor</label>
                            <select name="IDLeitor" class="form-control" required>
                                <option value="">Selecione</option>
                                @foreach($Leitor as $l)
                                <option value="{{$l->id}}" {{isset($Registro) && $Registro->IDLeitor == $l->id ? 'selected' : '' }}>{{$l->Nome}}</option>
                                @endforeach
                            </select>
                        </div>
                        @if(!isset($Registro))
                        <div class="col-sm-4">
                            <label>Livro</label>
                            <select name="IDLivro" class="form-control" required>
                                <option value="">Selecione</option>
                                @foreach($Livros as $lv)
                                <option value="{{$lv->id}}" {{isset($Registro) && $Registro->IDLivro == $lv->id ? 'selected' : '' }}>{{$lv->Livro}}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="col-sm-4">
                            <label>Devolução</label>
                            <input type="date" name="Devolucao" class="form-control" maxlength="50" required value="{{isset($Registro) ? $Registro->Devolucao : ''}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Observações</label>
                            <textarea name="Observacoes" class="form-control">{{isset($Registro) ? $Registro->Observacoes : ''}}</textarea>
                        </div>
                    </div>
                    <br>
                    <div class="col-sm-12 text-left row">
                        <button type="submit" class="btn btn-fr col-auto">Salvar</button>
                        &nbsp;
                        <a class="btn btn-light col-auto" href="{{route('Biblioteca/Emprestimos/index')}}">Voltar</a>
                    </div>
                </form>    
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>