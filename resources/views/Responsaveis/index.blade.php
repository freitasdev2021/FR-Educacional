<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <form action="{{route('Escolas/Relatorios/Gerar',"Responsaveis")}}" method="POST">
                @csrf
                @method('PUT');
                <h5>Escolha os dados do relatório</h5>
                <div class="col-sm-12 p-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Responsavel" name="Conteudo[]" id="flexCheckDefault">
                        <label class="form-check-label" for="flexCheckDefault">
                          Responsavel
                        </label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Aluno" name="Conteudo[]" id="flexCheckChecked">
                        <label class="form-check-label" for="flexCheckChecked">
                          Aluno
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Telefone" name="Conteudo[]" id="flexCheckChecked">
                        <label class="form-check-label" for="flexCheckChecked">
                          Telefone
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Escola" name="Conteudo[]" id="flexCheckChecked">
                        <label class="form-check-label" for="flexCheckChecked">
                          Escola
                        </label>
                    </div>
                </div>
                <hr>
                <button class="btn btn-primary col-auto">Gerar</button>
  
            </form>
        </div>
    </div>
</x-educacional-layout>