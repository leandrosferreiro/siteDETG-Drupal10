<?php

/**
 * @file
 * Cria tipos Institucional e Contato, campos, formulário de contato e conteúdo.
 *
 * Uso: ddev exec vendor/bin/drush php:script scripts/setup_info_pages.php
 */

use Drupal\contact\Entity\ContactForm;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\user\Entity\Role;

$say = static function (string $msg): void {
  echo $msg . PHP_EOL;
};

$ensure_type = static function (string $id, string $label, string $description) use ($say): void {
  $type = NodeType::load($id);
  if (!$type) {
    $type = NodeType::create([
      'type' => $id,
      'name' => $label,
      'description' => $description,
      'new_revision' => TRUE,
      'preview_mode' => 1,
      'display_submitted' => FALSE,
    ]);
    $type->setThirdPartySetting('menu_ui', 'available_menus', ['main']);
    $type->setThirdPartySetting('menu_ui', 'parent', 'main:');
    $type->save();
    $say("Tipo criado: $id");
  }
  else {
    $type->set('display_submitted', FALSE);
    $type->save();
    $say("Tipo já existia: $id");
  }
};

$ensure_storage = static function (string $name, string $type, int $cardinality = 1, array $settings = []) use ($say): void {
  if (FieldStorageConfig::loadByName('node', $name)) {
    return;
  }
  $values = [
    'field_name' => $name,
    'entity_type' => 'node',
    'type' => $type,
    'cardinality' => $cardinality,
    'translatable' => TRUE,
  ];
  if ($settings) {
    $values['settings'] = $settings;
  }
  FieldStorageConfig::create($values)->save();
  $say("  storage $name ($type)");
};

$ensure_instance = static function (string $bundle, string $name, string $label, string $description = '', bool $required = FALSE) use ($say): void {
  if (FieldConfig::loadByName('node', $bundle, $name)) {
    return;
  }
  FieldConfig::create([
    'field_name' => $name,
    'entity_type' => 'node',
    'bundle' => $bundle,
    'label' => $label,
    'description' => $description,
    'required' => $required,
    'translatable' => TRUE,
  ])->save();
  $say("  campo $bundle.$name");
};

$convert_bundle = static function (int $nid, string $bundle) use ($say): ?Node {
  $node = Node::load($nid);
  if (!$node) {
    $say("Nó $nid não encontrado.");
    return NULL;
  }
  if ($node->bundle() === $bundle) {
    $say("Nó $nid já é $bundle.");
    return $node;
  }
  $db = \Drupal::database();
  $db->update('node')->fields(['type' => $bundle])->condition('nid', $nid)->execute();
  $db->update('node_field_data')->fields(['type' => $bundle])->condition('nid', $nid)->execute();
  \Drupal::entityTypeManager()->getStorage('node')->resetCache([$nid]);
  \Drupal::service('entity_field.manager')->clearCachedFieldDefinitions();
  $node = Node::load($nid);
  $say("Nó $nid convertido para $bundle.");
  return $node;
};

$set_if_empty = static function (Node $node, string $field, $value): void {
  if (!$node->hasField($field)) {
    return;
  }
  if (!$node->get($field)->isEmpty()) {
    return;
  }
  $node->set($field, $value);
};

$ensure_type('institucional', 'Institucional', 'Página institucional do DETG (história, missão, estrutura e números). Prefira um único conteúdo publicado.');
$ensure_type('contato', 'Contato', 'Página de contato do DETG (endereço, telefone, e-mail, horário e formulário). Prefira um único conteúdo publicado.');

$ensure_storage('field_info_lead', 'string_long');
$ensure_storage('field_banner_titulo', 'string', 1, ['max_length' => 255]);
$ensure_storage('field_banner_texto', 'string', 1, ['max_length' => 255]);
$ensure_storage('field_resumo_historia', 'string_long');
$ensure_storage('field_historia', 'text_long');
$ensure_storage('field_resumo_missao', 'string_long');
$ensure_storage('field_missao', 'string_long');
$ensure_storage('field_visao', 'string_long');
$ensure_storage('field_valores', 'string_long');
$ensure_storage('field_estrutura_intro', 'string_long');
$ensure_storage('field_estrutura_nome', 'string', -1, ['max_length' => 255]);
$ensure_storage('field_estrutura_desc', 'string', -1, ['max_length' => 255]);
$ensure_storage('field_numero_anos', 'string', 1, ['max_length' => 32]);
$ensure_storage('field_numero_labs', 'string', 1, ['max_length' => 32]);
$ensure_storage('field_numero_docentes', 'string', 1, ['max_length' => 32]);
$ensure_storage('field_numero_projetos', 'string', 1, ['max_length' => 32]);
$ensure_storage('field_endereco', 'string_long');
$ensure_storage('field_contato_telefone', 'string', 1, ['max_length' => 64]);
$ensure_storage('field_horario', 'string_long');

$ensure_instance('institucional', 'field_info_lead', 'Chamada', 'Texto curto abaixo do título.');
$ensure_instance('institucional', 'field_banner_titulo', 'Título do banner');
$ensure_instance('institucional', 'field_banner_texto', 'Subtítulo do banner');
$ensure_instance('institucional', 'field_resumo_historia', 'Resumo — História', 'Aparece no card. Se vazio, o site usa um trecho da história.');
$ensure_instance('institucional', 'field_historia', 'História');
$ensure_instance('institucional', 'field_resumo_missao', 'Resumo — Missão', 'Aparece no card. Se vazio, o site usa a missão.');
$ensure_instance('institucional', 'field_missao', 'Missão');
$ensure_instance('institucional', 'field_visao', 'Visão');
$ensure_instance('institucional', 'field_valores', 'Valores');
$ensure_instance('institucional', 'field_estrutura_intro', 'Introdução da estrutura');
$ensure_instance('institucional', 'field_estrutura_nome', 'Itens da estrutura — nome', 'Um nome por linha (ex.: Chefia e vice-chefia). Mantenha a mesma ordem das descrições.');
$ensure_instance('institucional', 'field_estrutura_desc', 'Itens da estrutura — descrição', 'Uma descrição por linha, na mesma ordem dos nomes.');
$ensure_instance('institucional', 'field_numero_anos', 'Número — anos de história', 'Ex.: 60+');
$ensure_instance('institucional', 'field_numero_labs', 'Número — laboratórios', 'Ex.: 12');
$ensure_instance('institucional', 'field_numero_docentes', 'Número — docentes', 'Ex.: 40+');
$ensure_instance('institucional', 'field_numero_projetos', 'Número — projetos e parcerias', 'Ex.: 300+');

$ensure_instance('contato', 'field_info_lead', 'Chamada', 'Texto curto abaixo do título.');
$ensure_instance('contato', 'field_endereco', 'Endereço');
$ensure_instance('contato', 'field_contato_telefone', 'Telefone');
$ensure_instance('contato', 'field_e_mail', 'E-mail');
$ensure_instance('contato', 'field_horario', 'Horário de atendimento');

$configure_form = static function (string $bundle, array $components) use ($say): void {
  $id = "node.$bundle.default";
  $display = EntityFormDisplay::load($id);
  if (!$display) {
    $display = EntityFormDisplay::create([
      'targetEntityType' => 'node',
      'bundle' => $bundle,
      'mode' => 'default',
      'status' => TRUE,
    ]);
  }
  foreach ($components as $name => $component) {
    $display->setComponent($name, $component);
  }
  foreach (['promote', 'sticky'] as $hidden) {
    $display->removeComponent($hidden);
  }
  $display->save();
  $say("Form display $bundle atualizado.");
};

$textarea = static fn (int $weight, int $rows = 5): array => [
  'type' => 'string_textarea',
  'weight' => $weight,
  'settings' => ['rows' => $rows, 'placeholder' => ''],
];
$textfield = static fn (int $weight): array => [
  'type' => 'string_textfield',
  'weight' => $weight,
  'settings' => ['size' => 60, 'placeholder' => ''],
];

$configure_form('institucional', [
  'title' => ['type' => 'string_textfield', 'weight' => 0],
  'field_info_lead' => $textarea(1, 3),
  'field_banner_titulo' => $textfield(2),
  'field_banner_texto' => $textfield(3),
  'field_resumo_historia' => $textarea(10, 3),
  'field_historia' => [
    'type' => 'text_textarea',
    'weight' => 11,
    'settings' => ['rows' => 8, 'placeholder' => ''],
  ],
  'field_resumo_missao' => $textarea(20, 3),
  'field_missao' => $textarea(21, 4),
  'field_visao' => $textarea(22, 4),
  'field_valores' => $textarea(23, 4),
  'field_estrutura_intro' => $textarea(30, 4),
  'field_estrutura_nome' => $textfield(31),
  'field_estrutura_desc' => $textfield(32),
  'field_numero_anos' => $textfield(40),
  'field_numero_labs' => $textfield(41),
  'field_numero_docentes' => $textfield(42),
  'field_numero_projetos' => $textfield(43),
  'path' => ['type' => 'path', 'weight' => 50],
  'status' => ['type' => 'boolean_checkbox', 'weight' => 51, 'settings' => ['display_label' => TRUE]],
]);

$configure_form('contato', [
  'title' => ['type' => 'string_textfield', 'weight' => 0],
  'field_info_lead' => $textarea(1, 3),
  'field_endereco' => $textarea(2, 5),
  'field_contato_telefone' => $textfield(3),
  'field_e_mail' => [
    'type' => 'email_default',
    'weight' => 4,
    'settings' => ['placeholder' => 'detg@ufba.br', 'size' => 60],
  ],
  'field_horario' => $textarea(5, 3),
  'path' => ['type' => 'path', 'weight' => 20],
  'status' => ['type' => 'boolean_checkbox', 'weight' => 21, 'settings' => ['display_label' => TRUE]],
]);

foreach (['institucional', 'contato'] as $bundle) {
  $view = EntityViewDisplay::load("node.$bundle.default");
  if (!$view) {
    $view = EntityViewDisplay::create([
      'targetEntityType' => 'node',
      'bundle' => $bundle,
      'mode' => 'default',
      'status' => TRUE,
    ]);
  }
  $view->removeComponent('links');
  $view->save();
}

if (!ContactForm::load('detg')) {
  ContactForm::create([
    'id' => 'detg',
    'label' => 'Fale com o DETG',
    'recipients' => ['detg@ufba.br'],
    'reply' => '',
    'message' => 'Sua mensagem foi enviada à equipe do DETG.',
    'redirect' => '/contato',
    'weight' => 0,
  ])->save();
  $say('Formulário de contato detg criado.');
}
else {
  $form = ContactForm::load('detg');
  $form->setRecipients(['detg@ufba.br']);
  $form->setRedirectPath('/contato');
  $form->save();
  $say('Formulário de contato detg atualizado.');
}

$editor = Role::load('content_editor');
if ($editor) {
  $perms = [
    'create institucional content',
    'edit any institucional content',
    'delete any institucional content',
    'create contato content',
    'edit any contato content',
    'delete any contato content',
  ];
  foreach ($perms as $perm) {
    $editor->grantPermission($perm);
  }
  $editor->save();
  $say('Permissões do editor de conteúdo atualizadas.');
}

$inst = $convert_bundle(15, 'institucional');
if ($inst) {
  $set_if_empty($inst, 'field_info_lead', 'Conheça a história, missão e estrutura do Departamento de Engenharia de Transportes e Geodésia.');
  $set_if_empty($inst, 'field_banner_titulo', 'Departamento de Engenharia de Transportes e Geodésia');
  $set_if_empty($inst, 'field_banner_texto', 'Escola Politécnica da UFBA — formando engenheiros desde 1896');
  $set_if_empty($inst, 'field_resumo_historia', 'Criado na década de 1960 por iniciativa do Prof. Vasco de Azevedo Neto, o departamento consolidou-se como referência em transportes e geodésia na Bahia.');
  $set_if_empty($inst, 'field_historia', [
    'value' => '<p>O Departamento de Engenharia de Transportes e Geodésia foi criado na década de 1960, por iniciativa do Prof. Vasco de Azevedo Neto, no âmbito da Escola Politécnica da Universidade Federal da Bahia.</p><p>Desde então, o DETG integra ensino, pesquisa e extensão em Engenharia de Transportes e Geodésia, contribuindo para o desenvolvimento sustentável do país e para a qualificação das cidades e regiões.</p><p>O departamento está localizado no 6º andar da Escola Politécnica e oferece disciplinas para os cursos de graduação em Engenharia e em Tecnologia em Transportes Terrestre.</p>',
    'format' => 'basic_html',
  ]);
  $set_if_empty($inst, 'field_resumo_missao', 'Formar profissionais competentes e gerar conhecimento inovador para os desafios da mobilidade e do território.');
  $set_if_empty($inst, 'field_missao', 'Formar profissionais competentes e gerar conhecimento inovador para os desafios da mobilidade e do território.');
  $set_if_empty($inst, 'field_visao', 'Ser referência nacional em ensino, pesquisa e extensão em transportes e geodésia, a serviço da sociedade e do planejamento do território.');
  $set_if_empty($inst, 'field_valores', 'Ciência, tecnologia e inovação; compromisso público; cooperação acadêmica; e responsabilidade socioambiental.');
  $set_if_empty($inst, 'field_estrutura_intro', 'A gestão do DETG é exercida pela chefia e pela vice-chefia do departamento, com apoio dos colegiados, das comissões permanentes e dos núcleos de pesquisa e extensão.');
  $set_if_empty($inst, 'field_estrutura_nome', [
    'Chefia e vice-chefia',
    'Colegiados',
    'Comissões',
    'Laboratórios e núcleos',
  ]);
  $set_if_empty($inst, 'field_estrutura_desc', [
    'Direção administrativa e acadêmica do departamento.',
    'Instâncias de deliberação sobre ensino, pesquisa e extensão.',
    'Apoio à gestão, à avaliação e às atividades acadêmicas.',
    'Infraestrutura de pesquisa, extensão e formação prática.',
  ]);
  $set_if_empty($inst, 'field_numero_anos', '60+');
  $set_if_empty($inst, 'field_numero_labs', '12');
  $set_if_empty($inst, 'field_numero_docentes', '40+');
  $set_if_empty($inst, 'field_numero_projetos', '300+');
  $inst->setPublished();
  $inst->save();
  $say('Conteúdo institucional gravado (nid ' . $inst->id() . ').');
}

$contato = $convert_bundle(16, 'contato');
if ($contato) {
  $set_if_empty($contato, 'field_info_lead', 'Entre em contato com o DETG. Estamos à disposição para atendê-lo.');
  $set_if_empty($contato, 'field_endereco', "Escola Politécnica – UFBA\nDepartamento de Engenharia de Transportes e Geodésia\nRua Aristides Novis, 2 – 6º andar\nFederação, Salvador – BA\nCEP: 40210-630");
  $set_if_empty($contato, 'field_contato_telefone', '71 3283-9821');
  $set_if_empty($contato, 'field_e_mail', 'detg@ufba.br');
  $set_if_empty($contato, 'field_horario', "Segunda a sexta-feira\n8h às 12h e 13h às 17h");
  $contato->setPublished();
  $contato->save();
  $say('Conteúdo de contato gravado (nid ' . $contato->id() . ').');
}

$say('Concluído.');
