<?php

namespace Lkt\Console\Commands;

use Lkt\Instances\LktTranslation;
use Lkt\Translations\Enums\TranslationType;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupTranslationsCommand extends Command
{
    protected static $defaultName = 'lkt:translations:setup:i18n';

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Automatically generates all default translations')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $parent = LktTranslation::mkIfNot('adminHelper', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::mkIfNot('duplicatedText', TranslationType::Text, [
            'es' => '(Copia)',
            'en' => '(Copy)',
        ], $parentId);


        $parent = LktTranslation::mkIfNot('translationType', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::mkIfNot('text', TranslationType::Text, [
            'es' => 'Texto',
            'en' => 'Text',
        ], $parentId);
        LktTranslation::mkIfNot('textarea', TranslationType::Text, [
            'es' => 'Área de texto',
            'en' => 'Textarea',
        ], $parentId);
        LktTranslation::mkIfNot('many', TranslationType::Text, [
            'es' => 'Diccionario',
            'en' => 'Dictionary',
        ], $parentId);


        $parent = LktTranslation::mkIfNot('accessLevel', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::mkIfNot('1', TranslationType::Text, [
            'es' => 'Cualquiera',
            'en' => 'Any user',
        ], $parentId);
        LktTranslation::mkIfNot('2', TranslationType::Text, [
            'es' => 'Solo usuarios registrados',
            'en' => 'Only logged users',
        ], $parentId);
        LktTranslation::mkIfNot('3', TranslationType::Text, [
            'es' => 'Solo usuarios anónimos',
            'en' => 'Only anonymous users',
        ], $parentId);
        LktTranslation::mkIfNot('4', TranslationType::Text, [
            'es' => 'Solo administradores',
            'en' => 'Only admin users',
        ], $parentId);


        $parent = LktTranslation::mkIfNot('i18nForm', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::mkIfNot('property', TranslationType::Text, [
            'es' => 'Propiedad',
            'en' => 'Property',
        ], $parentId);
        LktTranslation::mkIfNot('type', TranslationType::Text, [
            'es' => 'Tipo',
            'en' => 'Type',
        ], $parentId);
        LktTranslation::mkIfNot('value', TranslationType::Text, [
            'es' => 'Valor',
            'en' => 'Value',
        ], $parentId);
        LktTranslation::mkIfNot('children', TranslationType::Text, [
            'es' => 'Traducciones',
            'en' => 'Translations',
        ], $parentId);
        LktTranslation::mkIfNot('addI18n', TranslationType::Text, [
            'es' => 'Agregar traducción',
            'en' => 'Add translation',
        ], $parentId);
        LktTranslation::mkIfNot('addI18nAndNew', TranslationType::Text, [
            'es' => 'Agregar traducción y seguir',
            'en' => 'Add translation and new',
        ], $parentId);
        LktTranslation::mkIfNot('addDictionary', TranslationType::Text, [
            'es' => 'Agregar diccionario',
            'en' => 'Add dictionary',
        ], $parentId);
        LktTranslation::mkIfNot('addDictionaryAndNew', TranslationType::Text, [
            'es' => 'Agregar diccionario y seguir',
            'en' => 'Add dictionary and new',
        ], $parentId);
        LktTranslation::mkIfNot('i18nTitleSingle', TranslationType::Text, [
            'es' => 'Traducción',
            'en' => 'Translation',
        ], $parentId);
        LktTranslation::mkIfNot('i18nTitleMany', TranslationType::Text, [
            'es' => 'Traducciones',
            'en' => 'Translations',
        ], $parentId);
        LktTranslation::mkIfNot('dictionaryTitleSingle', TranslationType::Text, [
            'es' => 'Diccionario',
            'en' => 'Dictionary',
        ], $parentId);
        LktTranslation::mkIfNot('dictionaryTitleMany', TranslationType::Text, [
            'es' => 'Diccionarios',
            'en' => 'Dictionaries',
        ], $parentId);

        $parent = LktTranslation::mkIfNot('userRoleCapability', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::mkIfNot('0', TranslationType::Text, [
            'es' => 'Deshabilitado',
            'en' => 'Disabled',
        ], $parentId);
        LktTranslation::mkIfNot('1', TranslationType::Text, [
            'es' => 'Elementos propios',
            'en' => 'Owned items',
        ], $parentId);
        LktTranslation::mkIfNot('2', TranslationType::Text, [
            'es' => 'Todos los elementos',
            'en' => 'All items',
        ], $parentId);


        $parent = LktTranslation::mkIfNot('userThemeModes', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::mkIfNot('0', TranslationType::Text, [
            'es' => 'Por defecto del sistema',
            'en' => 'System default',
        ], $parentId);
        LktTranslation::mkIfNot('1', TranslationType::Text, [
            'es' => 'Claro',
            'en' => 'Light',
        ], $parentId);
        LktTranslation::mkIfNot('2', TranslationType::Text, [
            'es' => 'Oscuro',
            'en' => 'Dark',
        ], $parentId);

        $parent = LktTranslation::mkIfNot('userForm', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::mkIfNot('firstName', TranslationType::Text, [
            'es' => 'Nombre',
            'en' => 'Name',
        ], $parentId);
        LktTranslation::mkIfNot('lastName', TranslationType::Text, [
            'es' => 'Apellidos',
            'en' => 'Lastname',
        ], $parentId);
        LktTranslation::mkIfNot('email', TranslationType::Text, [
            'es' => 'Email',
            'en' => 'Email',
        ], $parentId);
        LktTranslation::mkIfNot('addUser', TranslationType::Text, [
            'es' => 'Agregar usuario',
            'en' => 'Add user',
        ], $parentId);
        LktTranslation::mkIfNot('addUserAndNew', TranslationType::Text, [
            'es' => 'Agregar usuario y seguir',
            'en' => 'Add user and new',
        ], $parentId);
        LktTranslation::mkIfNot('titleSingle', TranslationType::Text, [
            'es' => 'Usuario',
            'en' => 'User',
        ], $parentId);
        LktTranslation::mkIfNot('titleMany', TranslationType::Text, [
            'es' => 'Usuarios',
            'en' => 'Users',
        ], $parentId);

        LktTranslation::mkIfNot('canReceiveMailNotifications', TranslationType::Text, [
            'es' => 'Recibir notificaciones por correo',
            'en' => 'Send mail notifications',
        ], $parentId);

        LktTranslation::mkIfNot('canReceivePushNotifications', TranslationType::Text, [
            'es' => 'Recibir notificaciones push',
            'en' => 'Send push notifications',
        ], $parentId);

        $parent = LktTranslation::mkIfNot('canReceiveMailNotificationsOptions', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::mkIfNot('0', TranslationType::Text, [
            'es' => 'Solo esenciales',
            'en' => 'Only essentials',
        ], $parentId);
        LktTranslation::mkIfNot('1', TranslationType::Text, [
            'es' => 'Todas',
            'en' => 'All',
        ], $parentId);

        $parent = LktTranslation::mkIfNot('canReceivePushNotificationsOptions', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::mkIfNot('0', TranslationType::Text, [
            'es' => 'Solo esenciales',
            'en' => 'Only essentials',
        ], $parentId);
        LktTranslation::mkIfNot('1', TranslationType::Text, [
            'es' => 'Todas',
            'en' => 'All',
        ], $parentId);


        $parent = LktTranslation::mkIfNot('userRoleForm', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::mkIfNot('name', TranslationType::Text, [
            'es' => 'Nombre',
            'en' => 'Name',
        ], $parentId);
        LktTranslation::mkIfNot('permissions', TranslationType::Text, [
            'es' => 'Permisos',
            'en' => 'Permissions',
        ], $parentId);
        LktTranslation::mkIfNot('component', TranslationType::Text, [
            'es' => 'Componente',
            'en' => 'Component',
        ], $parentId);
        LktTranslation::mkIfNot('ls', TranslationType::Text, [
            'es' => 'Listar elementos',
            'en' => 'List items',
        ], $parentId);
        LktTranslation::mkIfNot('mk', TranslationType::Text, [
            'es' => 'Crear elementos',
            'en' => 'Create items',
        ], $parentId);
        LktTranslation::mkIfNot('r', TranslationType::Text, [
            'es' => 'Leer elemento',
            'en' => 'Read item',
        ], $parentId);
        LktTranslation::mkIfNot('up', TranslationType::Text, [
            'es' => 'Actualizar elementos',
            'en' => 'Update items',
        ], $parentId);
        LktTranslation::mkIfNot('rm', TranslationType::Text, [
            'es' => 'Eliminar elementos',
            'en' => 'Drop items',
        ], $parentId);
        LktTranslation::mkIfNot('add', TranslationType::Text, [
            'es' => 'Agregar rol',
            'en' => 'Add role',
        ], $parentId);
        LktTranslation::mkIfNot('addAndNew', TranslationType::Text, [
            'es' => 'Agregar rol y seguir',
            'en' => 'Add role and new',
        ], $parentId);
        LktTranslation::mkIfNot('titleSingle', TranslationType::Text, [
            'es' => 'Rol de usuario',
            'en' => 'User role',
        ], $parentId);
        LktTranslation::mkIfNot('titleMany', TranslationType::Text, [
            'es' => 'Roles de usuario',
            'en' => 'User Roles',
        ], $parentId);



        $parent = LktTranslation::mkIfNot('menuEntryTypes', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::mkIfNot('1', TranslationType::Text, [
            'es' => 'URL local',
            'en' => 'Local URL',
        ], $parentId);
        LktTranslation::mkIfNot('2', TranslationType::Text, [
            'es' => 'URL completa',
            'en' => 'Full URL',
        ], $parentId);
        LktTranslation::mkIfNot('4', TranslationType::Text, [
            'es' => 'Listado de Páginas Web',
            'en' => 'Web Pages List',
        ], $parentId);
        LktTranslation::mkIfNot('6', TranslationType::Text, [
            'es' => 'Listado de Elementos Web',
            'en' => 'Web Items List',
        ], $parentId);
        LktTranslation::mkIfNot('8', TranslationType::Text, [
            'es' => 'Encabezado',
            'en' => 'Heading',
        ], $parentId);
        LktTranslation::mkIfNot('9', TranslationType::Text, [
            'es' => 'Submenú',
            'en' => 'Submenu',
        ], $parentId);
        LktTranslation::mkIfNot('10', TranslationType::Text, [
            'es' => 'Ruta de la app',
            'en' => 'App route',
        ], $parentId);


        $parent = LktTranslation::mkIfNot('accessLevel', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::mkIfNot('1', TranslationType::Text, [
            'es' => 'Cualquiera',
            'en' => 'Any user',
        ], $parentId);
        LktTranslation::mkIfNot('2', TranslationType::Text, [
            'es' => 'Solo usuarios registrados',
            'en' => 'Only logged users',
        ], $parentId);
        LktTranslation::mkIfNot('3', TranslationType::Text, [
            'es' => 'Solo usuarios anónimos',
            'en' => 'Only anonymous users',
        ], $parentId);
        LktTranslation::mkIfNot('4', TranslationType::Text, [
            'es' => 'Solo administradores',
            'en' => 'Only admin users',
        ], $parentId);


        $parent = LktTranslation::mkIfNot('webItems', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::mkIfNot('user', TranslationType::Text, [
            'es' => 'Usuarios',
            'en' => 'Users',
        ], $parentId);
        LktTranslation::mkIfNot('user-role', TranslationType::Text, [
            'es' => 'Roles de usuarios',
            'en' => 'User Roles',
        ], $parentId);
        LktTranslation::mkIfNot('menu', TranslationType::Text, [
            'es' => 'Menus',
            'en' => 'Menus',
        ], $parentId);
        LktTranslation::mkIfNot('menu-entry', TranslationType::Text, [
            'es' => 'Entradas de Menus',
            'en' => 'Menu Entries',
        ], $parentId);
        LktTranslation::mkIfNot('i18n', TranslationType::Text, [
            'es' => 'Traducciones',
            'en' => 'Translations',
        ], $parentId);
        LktTranslation::mkIfNot('many-i18n', TranslationType::Text, [
            'es' => 'Diccionario',
            'en' => 'Dictionary',
        ], $parentId);

        $parent = LktTranslation::mkIfNot('webPages', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::mkIfNot('page', TranslationType::Text, [
            'es' => 'Página',
            'en' => 'Page',
        ], $parentId);


        $parent = LktTranslation::mkIfNot('webPageStatus', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::mkIfNot('1', TranslationType::Text, [
            'es' => 'Pública',
            'en' => 'Public',
        ], $parentId);
        LktTranslation::mkIfNot('2', TranslationType::Text, [
            'es' => 'Borrador',
            'en' => 'Draft',
        ], $parentId);
        LktTranslation::mkIfNot('3', TranslationType::Text, [
            'es' => 'Programada',
            'en' => 'Scheduled',
        ], $parentId);


        $parent = LktTranslation::mkIfNot('webElement', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::mkOrUp('1', TranslationType::Text, [
            'es' => 'Texto libre',
            'en' => 'Free text',
        ], $parentId);
        LktTranslation::mkOrUp('2', TranslationType::Text, [
            'es' => 'Caja de texto',
            'en' => 'Text box',
        ], $parentId);
        LktTranslation::mkOrUp('3', TranslationType::Text, [
            'es' => 'Cuadrícula',
            'en' => 'Layout',
        ], $parentId);
        LktTranslation::mkOrUp('4', TranslationType::Text, [
            'es' => 'Caja con cuadrícula',
            'en' => 'Layout box',
        ], $parentId);
        LktTranslation::mkOrUp('5', TranslationType::Text, [
            'es' => 'Acordeón con texto',
            'en' => 'Text accordion',
        ], $parentId);
        LktTranslation::mkOrUp('6', TranslationType::Text, [
            'es' => 'Acordeón con cuadrícula',
            'en' => 'Layout accordion',
        ], $parentId);
        LktTranslation::mkOrUp('7', TranslationType::Text, [
            'es' => 'Banner',
            'en' => 'Banner',
        ], $parentId);
        LktTranslation::mkOrUp('8', TranslationType::Text, [
            'es' => 'Banner con cuadrícula',
            'en' => 'Layout Banner',
        ], $parentId);
        LktTranslation::mkOrUp('9', TranslationType::Text, [
            'es' => 'Enlace',
            'en' => 'Anchor',
        ], $parentId);
        LktTranslation::mkOrUp('10', TranslationType::Text, [
            'es' => 'Botón',
            'en' => 'Button',
        ], $parentId);
        LktTranslation::mkOrUp('11', TranslationType::Text, [
            'es' => 'Cabecera',
            'en' => 'Header',
        ], $parentId);
        LktTranslation::mkOrUp('12', TranslationType::Text, [
            'es' => 'Icono',
            'en' => 'Icon',
        ], $parentId);
        LktTranslation::mkOrUp('13', TranslationType::Text, [
            'es' => 'Iconos',
            'en' => 'Icons',
        ], $parentId);
        LktTranslation::mkOrUp('14', TranslationType::Text, [
            'es' => 'Multimedia',
            'en' => 'Multimedia',
        ], $parentId);


        $parent = LktTranslation::mkIfNot('layoutEngines', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::mkIfNot('grid', TranslationType::Text, [
            'es' => 'CSS Grid',
            'en' => 'CSS Grid',
        ], $parentId);
        LktTranslation::mkIfNot('flex-row', TranslationType::Text, [
            'es' => 'CSS Flex: Única Fila',
            'en' => 'CSS Flex: Single Row',
        ], $parentId);
        LktTranslation::mkIfNot('flex-rows', TranslationType::Text, [
            'es' => 'CSS Flex: Filas',
            'en' => 'CSS Flex: Rows',
        ], $parentId);
        LktTranslation::mkIfNot('flex-column', TranslationType::Text, [
            'es' => 'CSS Flex: Columna',
            'en' => 'CSS Flex: Column',
        ], $parentId);


        $parent = LktTranslation::mkIfNot('alignItemsOptions', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::mkIfNot('lkt-align-items-start', TranslationType::Text, [
            'es' => 'Por defecto: Inicio',
            'en' => 'Default: Start',
        ], $parentId);
        LktTranslation::mkIfNot('lkt-align-items-center', TranslationType::Text, [
            'es' => 'Por defecto: Centro',
            'en' => 'Default: Center',
        ], $parentId);
        LktTranslation::mkIfNot('lkt-align-items-end', TranslationType::Text, [
            'es' => 'Por defecto: Final',
            'en' => 'Default: End',
        ], $parentId);
        LktTranslation::mkIfNot('lkt-align-items-stretch', TranslationType::Text, [
            'es' => 'Por defecto: Estirado',
            'en' => 'Default: Stretch',
        ], $parentId);


        $parent = LktTranslation::mkIfNot('flexColumnsOptions', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::mkIfNot('lkt-flex-col-1', TranslationType::Text, [
            'es' => 'Por defecto: 1',
            'en' => 'Default: 1',
        ], $parentId);
        LktTranslation::mkIfNot('lkt-flex-col-2', TranslationType::Text, [
            'es' => 'Por defecto: 2',
            'en' => 'Default: 2',
        ], $parentId);
        LktTranslation::mkIfNot('lkt-flex-col-3', TranslationType::Text, [
            'es' => 'Por defecto: 3',
            'en' => 'Default: 3',
        ], $parentId);
        LktTranslation::mkIfNot('lkt-flex-col-4', TranslationType::Text, [
            'es' => 'Por defecto: 4',
            'en' => 'Default: 4',
        ], $parentId);
        LktTranslation::mkIfNot('lkt-flex-col-5', TranslationType::Text, [
            'es' => 'Por defecto: 5',
            'en' => 'Default: 5',
        ], $parentId);
        LktTranslation::mkIfNot('lkt-flex-col-6', TranslationType::Text, [
            'es' => 'Por defecto: 6',
            'en' => 'Default: 6',
        ], $parentId);
        LktTranslation::mkIfNot('lkt-flex-col-7', TranslationType::Text, [
            'es' => 'Por defecto: 7',
            'en' => 'Default: 7',
        ], $parentId);
        LktTranslation::mkIfNot('lkt-flex-col-8', TranslationType::Text, [
            'es' => 'Por defecto: 8',
            'en' => 'Default: 8',
        ], $parentId);
        LktTranslation::mkIfNot('lkt-flex-col-9', TranslationType::Text, [
            'es' => 'Por defecto: 9',
            'en' => 'Default: 9',
        ], $parentId);
        LktTranslation::mkIfNot('lkt-flex-col-10', TranslationType::Text, [
            'es' => 'Por defecto: 10',
            'en' => 'Default: 10',
        ], $parentId);
        LktTranslation::mkIfNot('lkt-flex-col-11', TranslationType::Text, [
            'es' => 'Por defecto: 11',
            'en' => 'Default: 11',
        ], $parentId);
        LktTranslation::mkIfNot('lkt-flex-col-12', TranslationType::Text, [
            'es' => 'Por defecto: 12',
            'en' => 'Default: 12',
        ], $parentId);
        LktTranslation::mkIfNot('lkt-flex-col-1--from-768', TranslationType::Text, [
            'es' => 'Desde 768px: 1',
            'en' => 'From 768px: 1',
        ], $parentId);
        LktTranslation::mkIfNot('lkt-flex-col-2--from-768', TranslationType::Text, [
            'es' => 'Desde 768px: 2',
            'en' => 'From 768px: 2',
        ], $parentId);
        LktTranslation::mkIfNot('lkt-flex-col-3--from-768', TranslationType::Text, [
            'es' => 'Desde 768px: 3',
            'en' => 'From 768px: 3',
        ], $parentId);
        LktTranslation::mkIfNot('lkt-flex-col-4--from-768', TranslationType::Text, [
            'es' => 'Desde 768px: 4',
            'en' => 'From 768px: 4',
        ], $parentId);
        LktTranslation::mkIfNot('lkt-flex-col-5--from-768', TranslationType::Text, [
            'es' => 'Desde 768px: 5',
            'en' => 'From 768px: 5',
        ], $parentId);


        $parent = LktTranslation::mkIfNot('justifyContentOptions', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::mkIfNot('lkt-justify-content-stretch', TranslationType::Text, [
            'es' => 'Por defecto: Ajustado',
            'en' => 'Default: Stretch',
        ], $parentId);
        LktTranslation::mkIfNot('lkt-justify-content-center', TranslationType::Text, [
            'es' => 'Por defecto: Centrado',
            'en' => 'Default: Center',
        ], $parentId);
        LktTranslation::mkIfNot('lkt-justify-content-space-between', TranslationType::Text, [
            'es' => 'Por defecto: Separar elementos',
            'en' => 'Default: Space between',
        ], $parentId);
        LktTranslation::mkIfNot('lkt-justify-content-space-around', TranslationType::Text, [
            'es' => 'Por defecto: Airear elementos',
            'en' => 'Default: Space around',
        ], $parentId);
        LktTranslation::mkIfNot('lkt-justify-content-space-evenly', TranslationType::Text, [
            'es' => 'Por defecto: Espacio equitativo',
            'en' => 'Default: Space evenly',
        ], $parentId);
        LktTranslation::mkIfNot('lkt-justify-content-start', TranslationType::Text, [
            'es' => 'Por defecto: Al inicio',
            'en' => 'Default: Start',
        ], $parentId);
        LktTranslation::mkIfNot('lkt-justify-content-end', TranslationType::Text, [
            'es' => 'Por defecto: Al final',
            'en' => 'Default: End',
        ], $parentId);

        $parent = LktTranslation::mkIfNot('accordionTypeOptions', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::mkIfNot('auto', TranslationType::Text, [
            'es' => 'Por defecto',
            'en' => 'Default',
        ], $parentId);
        LktTranslation::mkIfNot('always', TranslationType::Text, [
            'es' => 'Siempre abierto',
            'en' => 'Always opened',
        ], $parentId);
        LktTranslation::mkIfNot('lazy', TranslationType::Text, [
            'es' => 'Carga lenta',
            'en' => 'Lazy load',
        ], $parentId);
        LktTranslation::mkIfNot('ever', TranslationType::Text, [
            'es' => 'Carga siempre',
            'en' => 'Ever load',
        ], $parentId);

        $parent = LktTranslation::mkIfNot('bannerTypeOptions', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::mkIfNot('static', TranslationType::Text, [
            'es' => 'Fondo estático',
            'en' => 'Static background',
        ], $parentId);
        LktTranslation::mkIfNot('parallax', TranslationType::Text, [
            'es' => 'Fondo paralelo',
            'en' => 'Parallax background',
        ], $parentId);

        $parent = LktTranslation::mkIfNot('months', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::mkIfNot('1', TranslationType::Text, [
            'es' => 'Enero',
            'en' => 'January',
        ], $parentId);
        LktTranslation::mkIfNot('2', TranslationType::Text, [
            'es' => 'Febrero',
            'en' => 'February',
        ], $parentId);
        LktTranslation::mkIfNot('3', TranslationType::Text, [
            'es' => 'Marzo',
            'en' => 'March',
        ], $parentId);
        LktTranslation::mkIfNot('4', TranslationType::Text, [
            'es' => 'Abril',
            'en' => 'April',
        ], $parentId);
        LktTranslation::mkIfNot('5', TranslationType::Text, [
            'es' => 'Mayo',
            'en' => 'May',
        ], $parentId);
        LktTranslation::mkIfNot('6', TranslationType::Text, [
            'es' => 'Junio',
            'en' => 'June',
        ], $parentId);
        LktTranslation::mkIfNot('7', TranslationType::Text, [
            'es' => 'Julio',
            'en' => 'July',
        ], $parentId);
        LktTranslation::mkIfNot('8', TranslationType::Text, [
            'es' => 'Agosto',
            'en' => 'August',
        ], $parentId);
        LktTranslation::mkIfNot('9', TranslationType::Text, [
            'es' => 'Septiembre',
            'en' => 'September',
        ], $parentId);
        LktTranslation::mkIfNot('10', TranslationType::Text, [
            'es' => 'Octubre',
            'en' => 'October',
        ], $parentId);
        LktTranslation::mkIfNot('11', TranslationType::Text, [
            'es' => 'Noviembre',
            'en' => 'November',
        ], $parentId);
        LktTranslation::mkIfNot('12', TranslationType::Text, [
            'es' => 'Diciembre',
            'en' => 'December',
        ], $parentId);


        $parent = LktTranslation::mkIfNot('dayOfWeek', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::mkIfNot('1', TranslationType::Text, [
            'es' => 'Lunes',
            'en' => 'Monday',
        ], $parentId);
        LktTranslation::mkIfNot('2', TranslationType::Text, [
            'es' => 'Martes',
            'en' => 'Tuesday',
        ], $parentId);
        LktTranslation::mkIfNot('3', TranslationType::Text, [
            'es' => 'Miércoles',
            'en' => 'Wednesday',
        ], $parentId);
        LktTranslation::mkIfNot('4', TranslationType::Text, [
            'es' => 'Jueves',
            'en' => 'Thursday',
        ], $parentId);
        LktTranslation::mkIfNot('5', TranslationType::Text, [
            'es' => 'Viernes',
            'en' => 'Friday',
        ], $parentId);
        LktTranslation::mkIfNot('6', TranslationType::Text, [
            'es' => 'Sábado',
            'en' => 'Saturday',
        ], $parentId);
        LktTranslation::mkIfNot('7', TranslationType::Text, [
            'es' => 'Domingo',
            'en' => 'Sunday',
        ], $parentId);


        $parent = LktTranslation::mkIfNot('dayOfWeekAbbr', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::mkIfNot('1', TranslationType::Text, [
            'es' => 'Lu',
            'en' => 'Mo',
        ], $parentId);
        LktTranslation::mkIfNot('2', TranslationType::Text, [
            'es' => 'Ma',
            'en' => 'Tu',
        ], $parentId);
        LktTranslation::mkIfNot('3', TranslationType::Text, [
            'es' => 'Mi',
            'en' => 'We',
        ], $parentId);
        LktTranslation::mkIfNot('4', TranslationType::Text, [
            'es' => 'Ju',
            'en' => 'Th',
        ], $parentId);
        LktTranslation::mkIfNot('5', TranslationType::Text, [
            'es' => 'Vi',
            'en' => 'Fr',
        ], $parentId);
        LktTranslation::mkIfNot('6', TranslationType::Text, [
            'es' => 'Sa',
            'en' => 'Sa',
        ], $parentId);
        LktTranslation::mkIfNot('7', TranslationType::Text, [
            'es' => 'Do',
            'en' => 'Su',
        ], $parentId);


        $parent = LktTranslation::mkIfNot('buttons', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::mkIfNot('saveChanges', TranslationType::Text, [
            'es' => 'Guardar cambios',
            'en' => 'Save changes',
        ], $parentId);
        LktTranslation::mkIfNot('save', TranslationType::Text, [
            'es' => 'Guardar',
            'en' => 'Save',
        ], $parentId);
        LktTranslation::mkIfNot('drop', TranslationType::Text, [
            'es' => 'Eliminar',
            'en' => 'Delete',
        ], $parentId);
        LktTranslation::mkIfNot('back', TranslationType::Text, [
            'es' => 'Atrás',
            'en' => 'Back',
        ], $parentId);
        LktTranslation::mkIfNot('cancel', TranslationType::Text, [
            'es' => 'Cancelar',
            'en' => 'Cancel',
        ], $parentId);
        LktTranslation::mkIfNot('duplicate', TranslationType::Text, [
            'es' => 'Duplicar',
            'en' => 'Duplicate',
        ], $parentId);
        LktTranslation::mkIfNot('confirm', TranslationType::Text, [
            'es' => 'Confirmar',
            'en' => 'Confirm',
        ], $parentId);
        LktTranslation::mkIfNot('accept', TranslationType::Text, [
            'es' => 'Aceptar',
            'en' => 'Accept',
        ], $parentId);
        LktTranslation::mkIfNot('details', TranslationType::Text, [
            'es' => 'Detalles',
            'en' => 'Details',
        ], $parentId);
        LktTranslation::mkIfNot('editModeOn', TranslationType::Text, [
            'es' => 'Detener edición',
            'en' => 'Disable edition',
        ], $parentId);
        LktTranslation::mkIfNot('editModeOff', TranslationType::Text, [
            'es' => 'Editar',
            'en' => 'Enable edition',
        ], $parentId);
        LktTranslation::mkIfNot('actions', TranslationType::Text, [
            'es' => 'Acciones',
            'en' => 'Actions',
        ], $parentId);
        LktTranslation::mkIfNot('addElement', TranslationType::Text, [
            'es' => 'Añadir otro',
            'en' => 'Add element',
        ], $parentId);
        LktTranslation::mkIfNot('check', TranslationType::Text, [
            'es' => 'Comprobar',
            'en' => 'Check',
        ], $parentId);
        LktTranslation::mkIfNot('unlinkElement', TranslationType::Text, [
            'es' => 'Desvincular',
            'en' => 'Unlink',
        ], $parentId);

        return 1;
    }
}