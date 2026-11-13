<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSweetcronSchema extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('feeds')) {
            $this->forge->addField(['feed_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true], 'feed_title' => ['type' => 'TEXT'], 'feed_icon' => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => ''], 'feed_url' => ['type' => 'TEXT'], 'feed_data' => ['type' => 'LONGTEXT'], 'feed_status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'active'], 'feed_domain' => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => '']])->addKey('feed_id', true)->addKey('feed_status')->createTable('feeds');
        }
        if (! $this->db->tableExists('items')) {
            $this->forge->addField(['ID' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true], 'item_date' => ['type' => 'BIGINT'], 'item_content' => ['type' => 'LONGTEXT'], 'item_title' => ['type' => 'TEXT'], 'item_permalink' => ['type' => 'VARCHAR', 'constraint' => 2048, 'default' => ''], 'item_status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'publish'], 'item_name' => ['type' => 'VARCHAR', 'constraint' => 200, 'default' => ''], 'item_parent' => ['type' => 'BIGINT', 'default' => 0], 'item_data' => ['type' => 'LONGTEXT'], 'item_feed_id' => ['type' => 'INT', 'constraint' => 11, 'default' => 0]])->addKey('ID', true)->addKey(['item_status', 'item_date'])->addKey('item_feed_id')->createTable('items');
        }
        if (! $this->db->tableExists('options')) {
            $this->forge->addField(['option_id' => ['type' => 'BIGINT', 'auto_increment' => true], 'option_name' => ['type' => 'VARCHAR', 'constraint' => 64], 'option_value' => ['type' => 'LONGTEXT'], 'autoload' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'yes']])->addKey('option_id', true)->addKey('option_name')->createTable('options');
        }
        if (! $this->db->tableExists('tags')) {
            $this->forge->addField(['tag_id' => ['type' => 'BIGINT', 'auto_increment' => true], 'name' => ['type' => 'VARCHAR', 'constraint' => 55], 'slug' => ['type' => 'VARCHAR', 'constraint' => 200], 'count' => ['type' => 'BIGINT', 'default' => 0]])->addKey('tag_id', true)->addUniqueKey('slug')->createTable('tags');
        }
        if (! $this->db->tableExists('tag_relationships')) {
            $this->forge->addField(['item_id' => ['type' => 'BIGINT', 'default' => 0], 'tag_id' => ['type' => 'BIGINT', 'default' => 0]])->addKey(['item_id', 'tag_id'], true)->createTable('tag_relationships');
        }
        if (! $this->db->tableExists('users')) {
            $this->forge->addField(['ID' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true], 'user_login' => ['type' => 'VARCHAR', 'constraint' => 60], 'user_pass' => ['type' => 'VARCHAR', 'constraint' => 255], 'user_email' => ['type' => 'VARCHAR', 'constraint' => 100], 'user_activation_key' => ['type' => 'VARCHAR', 'constraint' => 60, 'default' => '']])->addKey('ID', true)->addKey('user_login')->createTable('users');
        }
    }

    public function down() {}
}
