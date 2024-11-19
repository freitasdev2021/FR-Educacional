<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <form action="{{route('Alunos/Save')}}" method="POST" enctype="multipart/form-data">
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
                @if(isset($Registro->IDAluno) && isset($Registro->IDMatricula))
                <input type="hidden" value="{{$Registro->IDAluno}}" name="IDAluno">
                <input type="hidden" value="{{$Registro->IDMatricula}}" name="IDMatricula">
                <input type="hidden" name="oldAnexoRG" value="{{$Registro->AnexoRG}}">
                <input type="hidden" name="oldRGPaisAnexo" value="{{$Registro->RGPaisAnexo}}">
                <input type="hidden" name="oldCResidencia" value="{{$Registro->CResidencia}}">
                <input type="hidden" name="oldCNascimento" value="{{$Registro->CNascimento}}">
                <input type="hidden" name="oldHistorico" value="{{$Registro->Historico}}">
                <input type="hidden" name="oldFoto" value="{{$Registro->Foto}}">
                <input type="hidden" name="CDPasta" value="{{$Registro->CDPasta}}">
                @endif
                <div class="col-sm-12 p-2">
                @if(in_array(Auth::user()->tipo,[4,4.5,2,2.5]))
                    <div>
                        <div class="d-flex justify-content-center mb-4">
                            <img id="selectedAvatar" src="{{ isset($Registro->Foto) ? url('storage/organizacao_' . Auth::user()->id_org . '_alunos/aluno_' . $Registro->CDPasta . '/' . $Registro->Foto) : asset('img/kidAvatar.png') }}"
                            class="rounded-circle" style="width: 200px; height: 200px; object-fit: cover;" alt="example placeholder" />
                        </div>
                        <div class="d-flex justify-content-center">
                            <div data-mdb-ripple-init class="btn btn-primary btn-rounded">
                                <label class="form-label text-white m-1" for="customFile2">Adicionar Foto</label>
                                <input type="file" name="Foto" class="form-control d-none" id="customFile2" onchange="displaySelectedImage(event, 'selectedAvatar')" accept="image/jpg,image/png,image/jpeg" {{!isset($Registro) ? '' : ''}} />
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Nome<strong class="text-danger">*</strong></label>
                            <input type="text" class="form-control" name="Nome" value="{{isset($Registro->Nome) ? $Registro->Nome : ''}}" required> 
                        </div>
                        <div class="col-sm-2">
                            <label>RG</label>
                            <input type="text" class="form-control" value="{{isset($Registro->RG) ? $Registro->RG : ''}}" name="RG"> 
                        </div>
                        <div class="col-sm-2">
                            <label>CPF</label>
                            <input type="text" class="form-control" value="{{isset($Registro->CPF) ? $Registro->CPF : ''}}" name="CPF"> 
                        </div>
                        <div class="col-sm-2">
                            <label>Email</label>
                            <input type="text" class="form-control" value="{{isset($Registro->Email) ? $Registro->Email : ''}}" name="Email"> 
                        </div>
                        <div class="col-sm-2">
                            <label>Nascimento<strong class="text-danger">*</strong></label>
                            <input type="date" class="form-control" value="{{isset($Registro->Nascimento) ? $Registro->Nascimento : ''}}" name="Nascimento" required> 
                        </div>
                        <div class="col-sm-2">
                            <label>Cartão do SUS</label>
                            <input type="text" class="form-control" value="{{isset($Registro->SUS) ? $Registro->SUS : ''}}" name="SUS"> 
                        </div>
                        <div class="col-sm-2">
                            <label>Passaporte</label>
                            <input type="text" class="form-control" value="{{isset($Registro->Passaporte) ? $Registro->Passaporte : ''}}" name="Passaporte"> 
                        </div>
                        <div class="col-sm-2">
                            <label>CNH</label>
                            <input type="text" class="form-control" value="{{isset($Registro->CNH) ? $Registro->CNH : ''}}" name="CNH"> 
                        </div>
                        <div class="col-sm-2">
                            <label>Naturalidade</label>
                            <input type="text" class="form-control" value="{{isset($Registro->Naturalidade) ? $Registro->Naturalidade : ''}}" name="Naturalidade"> 
                        </div>
                        <div class="col-sm-2">
                            <label>NIS</label>
                            <input type="number" class="form-control" value="{{isset($Registro->NIS) ? $Registro->NIS : ''}}" name="NIS"> 
                        </div>
                        <div class="col-sm-2">
                            <label>INEP</label>
                            <input type="number" class="form-control" value="{{isset($Registro->INEP) ? $Registro->INEP : ''}}" name="INEP"> 
                        </div>
                    </div>
                    <hr>
                    <h5>Dados do Responsável</h5>
                    <div class="row">
                        <div class="col-sm-2">
                        <label>Responsavel<strong class="text-danger">*</strong></label>
                        <input type="text" class="form-control" value="{{isset($Registro->NMResponsavel) ? $Registro->NMResponsavel : ''}}" name="NMResponsavel" required> 
                        </div>
                        <div class="col-sm-2">
                            <label>RG</label>
                            <input type="text" class="form-control" value="{{isset($Registro->RGPais) ? $Registro->RGPais : ''}}" name="RGPais"> 
                        </div>
                        <div class="col-sm-2">
                            <label>CPF<strong class="text-danger">*</strong></label>
                            <input type="text" class="form-control" value="{{isset($Registro->CPFResponsavel) ? $Registro->CPFResponsavel : ''}}" name="CPFResponsavel" required> 
                        </div>
                        <div class="col-sm-2">
                            <label>Email</label>
                            <input type="email" class="form-control" value="{{isset($Registro->EmailResponsavel) ? $Registro->EmailResponsavel : ''}}" name="EmailResponsavel"> 
                        </div>
                        <div class="col-sm-2">
                            <label>Profissão<strong class="text-danger">*</strong></label>
                            <input type="text" class="form-control" value="{{isset($Registro->Profissao) ? $Registro->Profissao : ''}}" name="Profissao" required> 
                        </div>
                        <div class="col-sm-2">
                            <label>Escolaridade<strong class="text-danger">*</strong></label>
                            <input type="text" class="form-control" value="{{isset($Registro->Escolaridade) ? $Registro->Escolaridade : ''}}" name="Escolaridade" required> 
                        </div>
                    </div>
                    <hr>
                    <h5>Dados dos Pais</h5>
                    <div class="row">
                        <div class="col-sm-3">
                            <label>Pai</label>
                            <input type="text" class="form-control" value="{{isset($Pais->Pai) ? $Pais->Pai : ''}}" name="Pai"> 
                        </div>
                        <div class="col-sm-2">
                            <label>RG</label>
                            <input type="text" class="form-control" value="{{isset($Pais->RGPai) ? $Pais->RGPai : ''}}" name="RGPai"> 
                        </div>
                        <div class="col-sm-2">
                            <label>CPF</label>
                            <input type="text" class="form-control" value="{{isset($Pais->CPFPai) ? $Pais->CPFPai : ''}}" name="CPFPai"> 
                        </div>
                        <div class="col-sm-3">
                            <label>Profissão</label>
                            <input type="text" class="form-control" value="{{isset($Pais->ProfissaoPai) ? $Pais->ProfissaoPai : ''}}" name="ProfissaoPai"> 
                        </div>
                        <div class="col-sm-2">
                            <label>Escolaridade</label>
                            <input type="text" class="form-control" value="{{isset($Pais->EscolaridadePai) ? $Pais->EscolaridadePai : ''}}" name="EscolaridadePai"> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <label>Mãe<strong class="text-danger">*</strong></label>
                            <input type="text" class="form-control" value="{{isset($Pais->Mae) ? $Pais->Mae : ''}}" name="Mae" required> 
                        </div>
                        <div class="col-sm-2">
                            <label>RG</label>
                            <input type="text" class="form-control" value="{{isset($Pais->RGMae) ? $Pais->RGMae : ''}}" name="RGMae"> 
                        </div>
                        <div class="col-sm-2">
                            <label>CPF</label>
                            <input type="text" class="form-control" value="{{isset($Pais->CPFMae) ? $Pais->CPFMae : ''}}" name="CPFMae"> 
                        </div>
                        <div class="col-sm-3">
                            <label>Profissão<strong class="text-danger">*</strong></label>
                            <input type="text" class="form-control" value="{{isset($Pais->ProfissaoMae) ? $Pais->ProfissaoMae : ''}}" name="ProfissaoMae" required> 
                        </div>
                        <div class="col-sm-2">
                            <label>Escolaridade<strong class="text-danger">*</strong></label>
                            <input type="text" class="form-control" value="{{isset($Pais->EscolaridadeMae) ? $Pais->EscolaridadeMae : ''}}" name="EscolaridadeMae" required> 
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-2">
                            <label>CEP<strong class="text-danger">*</strong></label>
                            <input type="text" name="CEP" class="form-control" required value="{{isset($Registro->CEP) ? $Registro->CEP : ''}}" required>
                        </div>
                        <div class="col-sm-5">
                            <label>Rua<strong class="text-danger">*</strong></label>
                            <input type="text" name="Rua" class="form-control" maxlength="50" value="{{isset($Registro->Rua) ? $Registro->Rua : ''}}" required>
                        </div>
                        <div class="col-sm-3">
                            <label>Bairro<strong class="text-danger">*</strong></label>
                            <input type="text" name="Bairro" class="form-control" maxlength="50" value="{{isset($Registro->Bairro) ? $Registro->Bairro : ''}}" minlength="2" required>
                        </div>
                        <div class="col-sm-1">
                            <label>UF<strong class="text-danger">*</strong></label>
                            <input type="text" name="UF" class="form-control" maxlength="2" value="{{isset($Registro->UF) ? $Registro->UF : ''}}" required>
                        </div>
                        <div class="col-sm-1">
                            <label>Numero</label>
                            <input type="text" name="Numero" class="form-control" maxlength="4" value="{{isset($Registro->Numero) ? $Registro->Numero : '0'}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Cidade<strong class="text-danger">*</strong></label>
                            <input type="text" name="Cidade" class="form-control" maxlength="50" value="{{isset($Registro->Cidade) ? $Registro->Cidade : ''}}" minlength="3" required>
                        </div>
                        <div class="col-sm-4">
                            <label>Celular Responsavel<strong class="text-danger">*</strong></label>
                            <input type="text" name="CLResponsavel" class="form-control" maxlength="50" value="{{isset($Registro->CLResponsavel) ? $Registro->CLResponsavel : ''}}" minlength="3" required>
                        </div>
                        <div class="col-sm-4">
                            <label>Celular Aluno</label>
                            <input type="text" name="Celular" class="form-control" maxlength="50" value="{{isset($Registro->Celular) ? $Registro->Celular : ''}}" minlength="3">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2">
                            <label>RG do Responsavel</label>
                            <input type="file" class="form-control" name="RGPaisAnexo" accept="application/pdf" {{!isset($Registro) ? '' : ''}}>
                        </div>
                        <div class="col-sm-2">
                            <label>RG do Aluno</label>
                            <input type="file" class="form-control" name="AnexoRG" accept="application/pdf" {{!isset($Registro) ? '' : ''}}>
                        </div>
                        <div class="col-sm-2">
                            <label>Comprovante de Residência</label>
                            <input type="file" class="form-control" name="CResidencia" accept="application/pdf" {{!isset($Registro) ? '' : ''}}>
                        </div>
                        <div class="col-sm-2">
                            <label>Histórico Escolar</label>
                            <input type="file" class="form-control" name="Historico" accept="application/pdf" {{!isset($Registro) ? '' : ''}}>
                        </div>
                        <div class="col-sm-4">
                            <label>Certidão de Nascimento</label>
                            <input type="file" class="form-control" name="CNascimento" accept="application/pdf" {{!isset($Registro) ? '' : ''}}>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <label>Turma<strong class="text-danger">*</strong></label>
                            <select class="form-control" name="IDTurma" required>
                                <option value="">Selecione</option>
                                @foreach($Turmas as $t)
                                <option value="{{$t->IDTurma}}" {{isset($Registro->IDTurma) && $Registro->IDTurma == $t->IDTurma ? 'selected' : ''}}>{{$t->Turma." (".$t->Serie.") - ". $t->Escola}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-1">
                            <label>Cor</label>
                            <select name="Cor" class="form-control">
                                <option value="branco" {{isset($Registro->Cor) && $Registro->Cor == "branco" ? 'selected' : ''}}>Branco</option>
                                <option value="preto" {{isset($Registro->Cor) && $Registro->Cor == "preto" ? 'selected' : ''}}>Preto</option>
                                <option value="pardo" {{isset($Registro->Cor) && $Registro->Cor == "pardo" ? 'selected' : ''}}>Pardo</option>
                                <option value="amarelo" {{isset($Registro->Cor) && $Registro->Cor == "amarelo" ? 'selected' : ''}}>Amarelo</option>
                                <option value="indigena" {{isset($Registro->Cor) && $Registro->Cor == "indigena" ? 'selected' : ''}}>Indígena</option>
                            </select>
                        </div>
                        <div class="col-sm-1">
                            <label>Sexo<strong class="text-danger">*</strong></label>
                            <select name="Sexo" class="form-control" required>
                                <option value="M" {{isset($Registro->Cor) && $Registro->Cor == "M" ? 'selected' : ''}}>Masculino</option>
                                <option value="F" {{isset($Registro->Cor) && $Registro->Cor == "F" ? 'selected' : ''}}>Feminino</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Necessidades Especiais</label>
                            <select class="form-control" name="NEE" >
                                <option value="1" {{isset($Registro->NEE) && $Registro->NEE == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->NEE) && $Registro->NEE == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <label>Acompanhamento Médico</label>
                            <select class="form-control" name="AMedico">
                                <option value="1" {{isset($Registro->AMedico) && $Registro->AMedico == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->AMedico) && $Registro->AMedico == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <label>Acompanhamento Psicológico</label>
                            <select class="form-control" name="APsicologico">
                                <option value="1" {{isset($Registro->APsicologico) && $Registro->APsicologico == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->APsicologico) && $Registro->APsicologico == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2">
                            <label>Tem Alergia?</label>
                            <select class="form-control" name="Alergia">
                                <option value="1" {{isset($Registro->Alergia) && $Registro->Alergia == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->Alergia) && $Registro->Alergia == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label>Utiliza Transporte Escolar?</label>
                            <select class="form-control" name="Transporte">
                                <option value="1" {{isset($Registro->Transporte) && $Registro->Transporte == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->Transporte) && $Registro->Transporte == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label>Recebe Bolsa Família?</label>
                            <select class="form-control" name="BolsaFamilia">
                                <option value="1" {{isset($Registro->BolsaFamilia) && $Registro->BolsaFamilia == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->BolsaFamilia) && $Registro->BolsaFamilia == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <label>Quilombola</label>
                            <select class="form-control" name="Quilombola">
                                <option value="1" {{isset($Registro->Quilombola) && $Registro->Quilombola == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->Quilombola) && $Registro->Quilombola == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Ensino Religioso</label>
                            <select class="form-control" name="EReligioso">
                                <option value="1" {{isset($Registro->EReligioso) && $Registro->EReligioso == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->EReligioso) && $Registro->EReligioso == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Educação Física</label>
                            <select class="form-control" name="EFisica">
                                <option value="1" {{isset($Registro->EFisica) && $Registro->EFisica == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->EFisica) && $Registro->EFisica == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Permite Utilização da Imagem</label>
                            <select class="form-control" name="DireitoImagem">
                                <option value="1" {{isset($Registro->DireitoImagem) && $Registro->DireitoImagem == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->DireitoImagem) && $Registro->DireitoImagem == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Observações</label>
                            <input type="text" name="Observacoes" class="form-control" value="{{isset($Registro) ? $Registro->Observacoes : ''}}">
                        </div>
                        <div class="col-sm-3">
                            <label>Tipo de Transporte</label>
                            <input type="text" name="TPTransporte" class="form-control" value="{{isset($Registro) ? $Registro->TPTransporte : ''}}">
                        </div>
                        <div class="col-sm-3">
                            <label>Data de Entrada</label>
                            <input type="date" name="DTEntrada" class="form-control" value="{{isset($Registro) ? $Registro->DTEntrada : ''}}">
                        </div>
                        <div class="checkboxEscolas">
                            <div class="form-check escola">
                                <br>
                                <input class="form-check-input" type="checkbox" value="1" name="credenciaisLogin" id="flexCheckDefault">
                                <label class="form-check-label" for="flexCheckDefault">
                                 Enviar Credenciais de Acesso ao Portal do Aluno com o E-mail do Aluno Informado na Matrícula
                                </label>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        @if(in_array(Auth::user()->tipo,[4,4.5,2,2.5]))
                        <div class="col-auto">
                            <button class="btn btn-fr">Salvar</button>
                        </div>
                        @if(isset($Registro) && $Vencimento->lt($Hoje) && $Registro->ANO <= date('Y'))
                        <div class="col-auto">
                            <button class="btn btn-warning text-white btn-renovar" type="button">Renovar</button>
                        </div>
                        @endif
                        <div class="col-auto">
                            <a class="btn btn-light" href="{{route('Alunos/index')}}">Cancelar</a>
                        </div>
                        @endif
                        @if(isset($Registro) && in_array(Auth::user()->tipo,[4,2,4.5,2.5])) 
                        <div class="col-auto">
                            <a href="{{route('Alunos/Comprovante/Matricula',$Registro->IDAluno)}}" class="btn btn-fr btn-xs">Declaração de Matrícula</a>
                            <a href="{{route('Alunos/Comprovante/Frequencia',$Registro->IDAluno)}}" class="btn btn-fr btn-xs">Declaração de Frequência</a>
                            <a href="{{route('Alunos/Comprovante/Filiacao',$Registro->IDAluno)}}" class="btn btn-fr btn-xs">Relatório de Matrícula</a>
                            <a href="{{route('Alunos/Comprovante/Conclusao',$Registro->IDAluno)}}" class="btn btn-fr btn-xs">Declaração de Conclusão</a>
                            <a href="{{route('Alunos/Comprovante/Escolaridade',$Registro->IDAluno)}}" class="btn btn-fr btn-xs">Declaração de Escolaridade</a>
                            <a href="{{route('Alunos/Comprovante/Vaga',$Registro->IDAluno)}}" class="btn btn-fr btn-xs">Atestado de Vaga</a>
                            <a href="{{route('Alunos/Comprovante/Prematricula',$Registro->IDAluno)}}" class="btn btn-fr btn-xs">Ficha de Pré Matrícula</a>
                            <a href="{{route('Alunos/Comprovante/Comparecimento',$Registro->IDAluno)}}" class="btn btn-fr btn-xs">Atestado de Comparecimento do Responsável</a>
                            <a href="{{route('Alunos/Comprovante/Responsabilidade',$Registro->IDAluno)}}" class="btn btn-fr btn-xs">Termo de Ciência e Responsabilidade</a>
                        </div>
                        @endif
                    </div>
                    @elseif(in_array(Auth::user()->tipo,[5.5,6.5,5,6]))
                    <div>
                        <div class="d-flex justify-content-center mb-4">
                            <img id="selectedAvatar" src="{{!isset($Registro->Foto) ? asset('img/kidAvatar.png') : url("storage/organizacao_".Auth::user()->id_org."alunos/aluno_$Registro->CDPasta/$Registro->Foto")}}"
                            class="rounded-circle" style="width: 200px; height: 200px; object-fit: cover;" alt="example placeholder" />
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Nome completo do Aluno</label>
                            <input type="text" class="form-control" name="Nome" value="{{isset($Registro->Nome) ? $Registro->Nome : ''}}" disabled> 
                        </div>
                        <div class="col-sm-2">
                            <label>RG do Aluno</label>
                            <input type="text" class="form-control" value="{{isset($Registro->RG) ? $Registro->RG : ''}}" name="RG" disabled> 
                        </div>
                        <div class="col-sm-2">
                            <label>CPF do Aluno</label>
                            <input type="text" class="form-control" value="{{isset($Registro->CPF) ? $Registro->CPF : ''}}" name="CPF" disabled> 
                        </div>
                        <div class="col-sm-2">
                            <label>Email do Aluno</label>
                            <input type="text" class="form-control" value="{{isset($Registro->Email) ? $Registro->Email : ''}}" name="Email" disabled> 
                        </div>
                        <div class="col-sm-2">
                            <label>Nascimento do Aluno</label>
                            <input type="date" class="form-control" value="{{isset($Registro->Nascimento) ? $Registro->Nascimento : ''}}" name="Nascimento" disabled> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                        <label>Nome completo do Responsavel</label>
                        <input type="text" class="form-control" value="{{isset($Registro->NMResponsavel) ? $Registro->NMResponsavel : ''}}" name="NMResponsavel" disabled> 
                        </div>
                        <div class="col-sm-3">
                            <label>RG do Responsavel</label>
                            <input type="text" class="form-control" value="{{isset($Registro->RGPais) ? $Registro->RGPais : ''}}" name="RGPais" disabled> 
                        </div>
                        <div class="col-sm-3">
                            <label>CPF do Responsavel</label>
                            <input type="text" class="form-control" value="{{isset($Registro->CPFResponsavel) ? $Registro->CPFResponsavel : ''}}" name="CPFResponsavel" disabled> 
                        </div>
                        <div class="col-sm-3">
                            <label>Email do Responsavel</label>
                            <input type="email" class="form-control" value="{{isset($Registro->EmailResponsavel) ? $Registro->EmailResponsavel : ''}}" name="EmailResponsavel" disabled> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2">
                            <label>CEP</label>
                            <input type="text" name="CEP" class="form-control" disabled value="{{isset($Registro->CEP) ? $Registro->CEP : ''}}">
                        </div>
                        <div class="col-sm-5">
                            <label>Rua</label>
                            <input type="text" name="Rua" class="form-control" maxlength="50" value="{{isset($Registro->Rua) ? $Registro->Rua : ''}}" disabled>
                        </div>
                        <div class="col-sm-3">
                            <label>Bairro</label>
                            <input type="text" name="Bairro" class="form-control" maxlength="50" value="{{isset($Registro->Bairro) ? $Registro->Bairro : ''}}" minlength="2" disabled>
                        </div>
                        <div class="col-sm-1">
                            <label>UF</label>
                            <input type="text" name="UF" class="form-control" maxlength="2" value="{{isset($Registro->UF) ? $Registro->UF : ''}}" disabled>
                        </div>
                        <div class="col-sm-1">
                            <label>Numero</label>
                            <input type="text" name="Numero" class="form-control" maxlength="4" value="{{isset($Registro->Numero) ? $Registro->Numero : ''}}" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Cidade</label>
                            <input type="text" name="Cidade" class="form-control" maxlength="50" value="{{isset($Registro->Cidade) ? $Registro->Cidade : ''}}" minlength="3" disabled>
                        </div>
                        <div class="col-sm-4">
                            <label>Celular Responsavel</label>
                            <input type="text" name="CLResponsavel" class="form-control" maxlength="50" value="{{isset($Registro->CLResponsavel) ? $Registro->CLResponsavel : ''}}" minlength="3" disabled>
                        </div>
                        <div class="col-sm-4">
                            <label>Celular Aluno</label>
                            <input type="text" name="Celular" class="form-control" maxlength="50" value="{{isset($Registro->Celular) ? $Registro->Celular : ''}}" minlength="3" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <label>Turma</label>
                            <select class="form-control" name="IDTurma" disabled>
                                <option value="">Selecione</option>
                                @foreach($Turmas as $t)
                                <option value="{{$t->id}}" {{isset($Registro->IDTurma) && $Registro->IDTurma == $t->id ? 'selected' : ''}}>{{$t->Nome." (".$t->Serie.")"}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Possui NEE</label>
                            <select class="form-control" name="NEE" disabled>
                                <option value="1" {{isset($Registro->NEE) && $Registro->NEE == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->NEE) && $Registro->NEE == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Acompanhamento Médico</label>
                            <select class="form-control" name="AMedico" disabled>
                                <option value="1" {{isset($Registro->AMedico) && $Registro->AMedico == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->AMedico) && $Registro->AMedico == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Acompanhamento Psicológico</label>
                            <select class="form-control" name="APsicologico" disabled>
                                <option value="1" {{isset($Registro->APsicologico) && $Registro->APsicologico == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->APsicologico) && $Registro->APsicologico == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Tem Alergia?</label>
                            <select class="form-control" name="Alergia" disabled>
                                <option value="1" {{isset($Registro->Alergia) && $Registro->Alergia == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->Alergia) && $Registro->Alergia == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label>Utiliza Transporte Escolar?</label>
                            <select class="form-control" name="Transporte" disabled>
                                <option value="1" {{isset($Registro->Transporte) && $Registro->Transporte == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->Transporte) && $Registro->Transporte == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label>Recebe Bolsa Família?</label>
                            <select class="form-control" name="BolsaFamilia" disabled>
                                <option value="1" {{isset($Registro->BolsaFamilia) && $Registro->BolsaFamilia == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->BolsaFamilia) && $Registro->BolsaFamilia == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                    </div>
                    @endif
                </div>
            </form>
            @if(isset($Registro) && $Vencimento->lt($Hoje) && $Registro->ANO <= date('Y'))
            <form class="form-controls" id="formRenova" method="POST" action="{{route('Alunos/Renovar')}}" style="display:hidden">
                @csrf
                <input type="hidden" name="IDAluno" value="{{$Registro->IDAluno}}">  
            </form>
            @endif
        </div>
    </div>
    <script>
        $('input[name=CEP]').on("change",function(e){
            if( $(this).val().length == 9){
                var cep = $(this).val();
                var url = "https://viacep.com.br/ws/"+cep+"/json/";
                $.ajax({
                    url: url,
                    type: 'get',
                    dataType: 'json',
                    success: function(dados){
                        $("input[name=UF]").val(dados.uf).change();
                        $("input[name=Cidade]").val(dados.localidade);
                        $("input[name=Bairro]").val(dados.bairro);
                        $("input[name=Rua]").val(dados.logradouro);
                    }
                })
            }            
        })
        //
        $("input[name=CEP]").inputmask('99999-999')
        $("input[name=Celular]").inputmask('(99) 9 9999-9999')
        $("input[name=CLResponsavel]").inputmask('(99) 9 9999-9999')
        $("input[name=CPF]").inputmask('999.999.999-99')
        $("input[name=CPFAluno]").inputmask('999.999.999-99')
        $("input[name=CPFResponsavel]").inputmask('999.999.999-99')
        $("input[name=RGResponsavel]").inputmask('99-999-999')
        $("input[name=RGPais]").inputmask('99.999.999')
        $("input[name=RG]").inputmask('99.999.999')
        //
        function displaySelectedImage(event, elementId) {
            const selectedImage = document.getElementById(elementId);
            const fileInput = event.target;

            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    selectedImage.src = e.target.result;
                };

                reader.readAsDataURL(fileInput.files[0]);
            }
        }
        //
        $('.btn-renovar').on("click",function(){
            if(confirm("Deseja Renovar a Matrícula do Aluno?")){
                $("#formRenova").submit()
            }
        })
        //
    </script>
</x-educacional-layout>