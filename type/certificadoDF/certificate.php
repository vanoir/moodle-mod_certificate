<?php

// This file is part of the Certificate module for Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * A4_embedded certificate type
 *
 * @package    mod_certificate
 * @copyright  Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$pdf = new PDF($certificate->orientation, 'mm', 'A4', true, 'UTF-8', false);

$pdf->SetTitle($certificate->name);
$pdf->SetProtection(array('modify'));
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetAutoPageBreak(false, 0);
$pdf->AddPage();

// Define variables
// Landscape
if ($certificate->orientation == 'L') {
    $x = 10;
    $y = 30;
    $sealx = 230;
    $sealy = 150;
    $sigx = 47;
    $sigy = 155;
    $custx = 47;
    $custy = 155;
    $wmarkx = 40;
    $wmarky = 31;
    $wmarkw = 212;
    $wmarkh = 148;
    $brdrx = 0;
    $brdry = 0;
    $brdrw = 297;
    $brdrh = 210;
    $codey = 175;
} else { // Portrait
    $x = 10;
    $y = 40;
    $sealx = 150;
    $sealy = 220;
    $sigx = 30;
    $sigy = 230;
    $custx = 30;
    $custy = 230;
    $wmarkx = 26;
    $wmarky = 58;
    $wmarkw = 158;
    $wmarkh = 170;
    $brdrx = 0;
    $brdry = 0;
    $brdrw = 210;
    $brdrh = 297;
    $codey = 250;
}

// Get font families.
$fontsans = get_config('certificate', 'fontsans');
$fontserif = get_config('certificate', 'fontserif');

// Add images and lines
certificate_print_image($pdf, $certificate, CERT_IMAGE_BORDER, $brdrx, $brdry, $brdrw, $brdrh);
certificate_draw_frame($pdf, $certificate);
// Set alpha to semi-transparency
$pdf->SetAlpha(0.2);
certificate_print_image($pdf, $certificate, CERT_IMAGE_WATERMARK, $wmarkx, $wmarky, $wmarkw, $wmarkh);
$pdf->SetAlpha(1);
certificate_print_image($pdf, $certificate, CERT_IMAGE_SEAL, $sealx, $sealy, '', '');
certificate_print_image($pdf, $certificate, CERT_IMAGE_SIGNATURE, $sigx, $sigy, '', '');

certificate_print_text($pdf, $x + 20, $y + 20, 'C', 'freeserif', '', 19, "O <b>Departamento de Doenças Sexualmente Transmissíveis, Aids e Hepatites Virais</b>, e a <b>Coordenação Geral de Sangue e Hemoderivados</b> certifica 
que <b>".fullname($USER)."</b> participou e obteve aprovação no curso <b>".$course->fullname."</b> do Sistema TELELAB de Educação Permanente, 
em <b>".str_replace(" ", " de ", certificate_get_date($certificate, $certrecord, $course))."</b>, com carga horária de <b>30 horas</b>.", 220);


certificate_print_text($pdf, $x, $y+120, 'C', 'freeserif', '', 10, "A autenticidade deste documento pode ser verificada em \"www.telelab.aids.gov.br/verificar\" informando a senha: ".certificate_get_code($certificate, $certrecord));

//printando o conteudo programatico
$xProg = $x+8;
$yProg = $y+63;
foreach(getProgramatico($course->id) as $linhasProgramatico){
$yProg = $yProg+4;
certificate_print_text($pdf, $xProg, $yProg+10, 'L', 'freeserif', '', 10, $linhasProgramatico);
}




/* Add text 
$pdf->SetTextColor(0, 0, 120);
certificate_print_text($pdf, $x, $y, 'C', 'freesans', '', 30, get_string('title', 'certificate'));
$pdf->SetTextColor(0, 0, 0);
certificate_print_text($pdf, $x, $y + 36, 'C', 'freesans', '', 30, fullname($USER));
certificate_print_text($pdf, $x, $y + 55, 'C', 'freesans', '', 20, get_string('statement', 'certificate'));
certificate_print_text($pdf, $x, $y + 72, 'C', 'freesans', '', 20, $course->fullname);
certificate_print_text($pdf, $x, $y + 92, 'C', 'freesans', '', 14,  certificate_get_date($certificate, $certrecord, $course));
certificate_print_text($pdf, $x, $y + 102, 'C', 'freeserif', '', 10, certificate_get_grade($certificate, $course));
certificate_print_text($pdf, $x, $y + 112, 'C', 'freeserif', '', 10, certificate_get_outcome($certificate, $course));
if ($certificate->printhours) {
    certificate_print_text($pdf, $x, $y + 122, 'C', 'freeserif', '', 10, get_string('credithours', 'certificate') . ': ' . $certificate->printhours);
}
*/
$i = 0;
if ($certificate->printteacher) {
    $context = context_module::instance($cm->id);
    if ($teachers = get_users_by_capability($context, 'mod/certificate:printteacher', '', $sort = 'u.lastname ASC', '', '', '', '', false)) {
        foreach ($teachers as $teacher) {
            $i++;
            certificate_print_text($pdf, $sigx, $sigy + ($i * 4), 'L', 'freeserif', '', 12, fullname($teacher));
        }
    }
}

certificate_print_text($pdf, $custx, $custy, 'L', null, null, null, $certificate->customtext);


function getIDCursos(){

return   array(
"idBiosseg" => 5,
"idColeta" => 3,
"idCultura" => 12,
"idChagas" => 14,
"idEquipamentos" => 8,
"idGram" => 10,
"idTuberculose" => 13,
"idDiagnosticoHIV" => 2,
"idDiagnosticoSifilis" => 4,
"idDiagnosticoHepatites"=> 9,
"idAEQ"=> 16,
"idDF"=> 15,
"idFTR"=> 17);
}

function getProgramatico($idCurso){
extract(getIDCursos());
    switch($idCurso) {

        case $idFTR:
            return array("&#8226; ?????????????.",
                        "&#8226; Painel AEQ-TR.",
                        "&#8226; Recebimento e hidratação.",
                        "&#8226; Envio dos resultados.",
                        "&#8226; Dos resultados aos relatórios de desempenho individual e ao relatório global.",
                        "&#8226; Certificados.");
            break;

        case $idAEQ:
            return array("&#8226; Programa de Avaliação Externa da Qualidade para Testes Rápidos (AEQ-TR).",
                        "&#8226; Painel AEQ-TR.",
                        "&#8226; Recebimento e hidratação.",
                        "&#8226; Envio dos resultados.",
                        "&#8226; Dos resultados aos relatórios de desempenho individual e ao relatório global.",
                        "&#8226; Certificados.");
            break;

        case $idBiosseg:

            return array("&#8226; Prevenir riscos ocupacionais em ambientes de laboratórios através da adoção de boas práticas.",
                        "&#8226; Higienizar as mãos como forma de prevenir as infecções cruzadas.",
                        "&#8226; Relacionar os direitos e obrigações de trabalhadores e de empregadores com referência ao fornecimento e uso de Equipamentos de Proteção Individual.",
                        "&#8226; Estabelecer relação entre riscos biológicos e a utilização de Equipamentos de Proteção Coletiva.",
                        "&#8226; Realizar o processo de descontaminação, desinfecção e limpeza da área de trabalho, de materiais e de resíduos em situações de acidentes em laboratórios.",
                        "&#8226; Identificar, separar, descontaminar e descartar os resíduos de laboratório de acordo com a legislação sanitária referente.");
                        break;

        case $idColeta:
            return array("&#8226; Produzir, utilizar e manter atualizado os Procedimentos Operacionais Padrão para coleta de sangue. Acolher, orientar e cadastrar os pacientes.",
                        "&#8226; Organizar o local de trabalho e separar o material necessário para coleta para cada exame laboratorial.",
                        "&#8226; Realizar o procedimento para coleta de sangue por sistema a vácuo.",
                        "&#8226; Realizar os procedimentos para coleta, processamento, armazenamento e transporte de amostras.",
                        "&#8226; Realizar o procedimento para coleta de sangue em papel filtro.",
                        "&#8226; Preparar amostras para transporte.");
                        break;

        case $idCultura:
            return array("&#8226; Preparação de meios de cultura para o isolamento de Neisseria gonorrhoeae: meio de transporte de Amies, Thayer Martin modificado e ágar chocolate. ",
                        "&#8226; Preparação de meios e reagentes, para a identificação de Neisseria gonorrhoeae: prova da oxidase e meios para fermentação de açúcares.",
                        "&#8226; O agente etiológico, a Neisseria gonorrhoeae.",
			"&#8226; Cuidados no isolamento e na identificação de Neisseria gonorrhoeae.",
			"&#8226; A identificação presuntiva e a confirmatória no diagnóstico laboratorial de Neisseria gonorrhoeae.",
                        "&#8226; A prova de resistência à penicilina.");
                        break;

        case $idChagas:
            return array("&#8226; Conhecer a origem, o histórico e as características gerais dos testes para detecção da infecção por T. cruzi.",
                        "&#8226; Conhecer os mecanismos de transmissão e infecção por T. cruzi.",
                        "&#8226; Conhecer os métodos de detecção da infecção por T. cruzi e suas características.",
                        "&#8226; Conhecer e utilizar os métodos, procedimentos e protocolos do diagnóstico da Doença de Chagas em laboratórios de Saúde Pública.",
                        "&#8226; Conhecer e utilizar os princípios de reações sorológicas para realização dos testes para diagnóstico da Doença de Chagas.",
			"&#8226; Conhecer e utilizar as técnicas necessárias para realização dos testes para diagnóstico da Doença de Chagas.",
			"&#8226; Conhecer e utilizar os cuidados e protocolos na interpretação de resultados e procedimentos correlatos na realização do diagnóstico da Doença de Chagas.",
                        "&#8226; Conhecer e realizar os procedimentos de controle de qualidade dos métodos de diagnóstico da Doença de Chagas.");
                        break;

        case $idEquipamentos:
            return array("&#8226; Utilização e conservação de centrífugas, geladeiras. Microscópios, homogeneizadores de bolsas e outros equipamentos utilizados em Laboratórios e Unidades Hemoterápicas. ",
                        "&#8226; Monitoramento e controle de equipamentos termo-controláveis.",
                        "&#8226; Pipetas, utilização e controle de pipetagens.");
                        break;

        case $idGram:
            return array("&#8226; Conhecer a origem, o histórico e as características gerais do método de coloração de Gram.",
                        "&#8226; Conhecer a caracterização, a utilização, a eficácia, a forma de interpretação, os recursos para realização e o sistemas de classificação do método de coloração de Gram.",
                        "&#8226; Conhecer as modificações do método de coloração de Gram e suas vantagens.",
                        "&#8226; Conhecer e utilizar os passos de preparação dos reagentes necessários à realização do método de coloração de Gram.",
                        "&#8226; Conhecer e utilizar os passos da realização do método de coloração de Gram.",
                        "&#8226; Conhecer e utilizar as técnicas necessárias ao controle de qualidade do método de coloração de Gram.");
                        break;

        case $idTuberculose:
            return array("&#8226; Conhecer a patogênese do Mycobacterium tuberculosis na tuberculose pulmonar. ",
                        "&#8226; Compreender as etapas da baciloscopia como processo de diagnóstico da tuberculose pulmonar: a coleta e o transporte de escarro e a realização do esfregaço. ",
			"&#8226; Conhecer a patogênese do Mycobacterium tuberculosis na tuberculose pulmonar. ",
			"&#8226; Compreender a leitura e interpretação dos resultados da baciloscopia no diagnóstico da tuberculose pulmonar.",
			"&#8226; Conhecer procedimentos de controle e avaliação da qualidade utilizada pelos Laboratórios de Saúde Pública na baciloscopia para o controle da tuberculose.",
                        "&#8226; Conhecer as fórmulas de preparação de soluções utilizadas na baciloscopia para o controle da tuberculose.");
                        break;

        case $idDiagnosticoHIV:
            return array("&#8226; Identificar a estrutura, as proteínas e as enzimas do HIV envolvidas no processo de multiplicação viral importantes para o diagnóstico laboratorial.",
                        "&#8226; Identificar o processo de replicação viral para relacionar com a patogênese viral e com o diagnóstico laboratorial.",
                        "&#8226; Identificar a sequência de surgimento dos marcadores sanguíneos decorrentes da infecção e a relação ao diagnóstico sorológico.",
                        "&#8226; Identificar os marcadores detectáveis nos testes sorológicos.",
                        "&#8226; Identificar os princípios metodológicos de testes sorológicos utilizados nas Etapas do Diagnóstico Laboratorial da Infecção pelo HIV.",
                        "&#8226; Aplicar a Portaria 151 SVS/MS e seus fluxogramas na rotina de trabalho.");
                        break;

        case $idDF:
            return array("&#8226; Origem e evolução do conhecimento científico.",
                        "&#8226; A dispersão do gene da Hb S.",
                        "&#8226; Herança genética.",
                        "&#8226; Perfil demográfico da Doença Falciforme no Brasil.",
                        "&#8226; Fisiopatologia.",
                        "&#8226; Manifestações clínicas.",
                        "&#8226; Manifestações clínicas na gestação e cuidados da gestante com Doença Falciforme.",
                        "&#8226; Diagnóstico laboratorial.",
                        "&#8226; Doença falciforme nas Redes de Atenção à Saúde.");
                        break;

        case $idDiagnosticoSifilis:
            return array("&#8226; Contextualizar histórica e socialmente a Sífilis.",
                        "&#8226; Relacionar as fases da doença com o diagnóstico laboratorial e reconhecer o agente etiológico da sífilis.",
                        "&#8226; Identificar o princípio metodológico dos testes de floculação.",
                        "&#8226; Preparar a suspensão antigênica para realizar o teste de VDRL.",
                        "&#8226; Validar a suspensão antigênica para assegurar a qualidade da suspensão antigênica para reação do VDRL.",
                        "&#8226; Realizar o teste VDRL qualitativo para evitar o fenômeno prozona; Aplicar o teste quantitativo das amostras reagentes no teste qualitativo; Ler e interpretar o teste e emitir laudo;");
                        break;

        case $idDiagnosticoHepatites:
            return array("&#8226; Identificar os vírus causadores de Hepatites Virais.",
                        "&#8226; Conhecer os marcadores sorológicos das infecções por Vírus das Hepatites B e C.",
                        "&#8226; Identificar os princípios metodológicos dos testes rápidos utilizados no diagnóstico das Hepatites B e C.",
                        "&#8226; Compreender a coleta de sangue por punção digital para a realização de testes rápidos.",
                        "&#8226; Ler e interpretar testes rápidos para o diagnóstico das Hepatites B e C.");
                        break;
	}

}

?>
