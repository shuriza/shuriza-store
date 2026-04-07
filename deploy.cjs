#!/usr/bin/env node
/**
 * Shuriza Store - Deployment Helper Script
 * 
 * Penggunaan:
 *   node deploy.cjs           - Deploy interaktif
 *   node deploy.cjs --quick   - Deploy cepat (tanpa konfirmasi)
 *   npm run deploy            - Sama dengan node deploy.cjs
 */

const { execSync, spawn } = require('child_process');
const readline = require('readline');
const fs = require('fs');
const path = require('path');

// Konfigurasi
const CONFIG_FILE = path.join(__dirname, '.deploy-config.json');
const DEFAULT_CONFIG = {
    host: '68.183.182.39',
    user: 'root',
    path: '/var/www/shuriza-store',
    branch: 'main'
};

// Warna untuk output terminal
const colors = {
    reset: '\x1b[0m',
    bright: '\x1b[1m',
    red: '\x1b[31m',
    green: '\x1b[32m',
    yellow: '\x1b[33m',
    blue: '\x1b[34m',
    cyan: '\x1b[36m',
    magenta: '\x1b[35m'
};

function log(message, color = 'reset') {
    console.log(`${colors[color]}${message}${colors.reset}`);
}

function logStep(step, message) {
    console.log(`${colors.cyan}[${step}]${colors.reset} ${message}`);
}

function logSuccess(message) {
    console.log(`${colors.green}✓${colors.reset} ${message}`);
}

function logError(message) {
    console.log(`${colors.red}✗${colors.reset} ${message}`);
}

function logWarning(message) {
    console.log(`${colors.yellow}⚠${colors.reset} ${message}`);
}

// Load atau buat config
function loadConfig() {
    try {
        if (fs.existsSync(CONFIG_FILE)) {
            const saved = JSON.parse(fs.readFileSync(CONFIG_FILE, 'utf8'));
            return { ...DEFAULT_CONFIG, ...saved };
        }
    } catch (e) {
        logWarning('Tidak bisa load config, pakai default');
    }
    return DEFAULT_CONFIG;
}

function saveConfig(config) {
    try {
        fs.writeFileSync(CONFIG_FILE, JSON.stringify(config, null, 2));
        logSuccess('Konfigurasi tersimpan');
    } catch (e) {
        logWarning('Tidak bisa simpan config');
    }
}

// Buat readline interface
function createRL() {
    return readline.createInterface({
        input: process.stdin,
        output: process.stdout
    });
}

async function question(rl, prompt, defaultValue = '') {
    return new Promise((resolve) => {
        const defaultText = defaultValue ? ` (${defaultValue})` : '';
        rl.question(`${colors.yellow}?${colors.reset} ${prompt}${defaultText}: `, (answer) => {
            resolve(answer.trim() || defaultValue);
        });
    });
}

async function confirm(rl, prompt) {
    const answer = await question(rl, `${prompt} (y/n)`, 'y');
    return answer.toLowerCase() === 'y' || answer.toLowerCase() === 'yes';
}

// Cek status git lokal
function checkGitStatus() {
    try {
        const status = execSync('git status --porcelain', { encoding: 'utf8' });
        return status.trim() === '';
    } catch (e) {
        return false;
    }
}

function getCurrentBranch() {
    try {
        return execSync('git branch --show-current', { encoding: 'utf8' }).trim();
    } catch (e) {
        return 'tidak diketahui';
    }
}

function getLastCommit() {
    try {
        return execSync('git log -1 --pretty=format:"%h - %s"', { encoding: 'utf8' }).trim();
    } catch (e) {
        return 'tidak diketahui';
    }
}

// Push perubahan lokal
function pushChanges(branch, targetBranch) {
    logStep('PUSH', `Mendorong ${branch} ke ${targetBranch}...`);
    try {
        execSync(`git push origin ${branch}:${targetBranch}`, { stdio: 'inherit' });
        logSuccess('Perubahan berhasil di-push');
        return true;
    } catch (e) {
        logError('Gagal push perubahan');
        return false;
    }
}

// Generate perintah SSH
function generateDeployCommands(config, options = {}) {
    const commands = [];
    
    commands.push(`cd ${config.path}`);
    commands.push(`git pull origin ${config.branch}`);
    
    if (options.migrate) {
        commands.push('php artisan migrate --force');
    }
    
    if (options.seed) {
        commands.push('php artisan db:seed --force');
    }
    
    if (options.clearCache) {
        commands.push('php artisan cache:clear');
        commands.push('php artisan config:clear');
        commands.push('php artisan view:clear');
        commands.push('php artisan route:clear');
    }
    
    if (options.optimize) {
        commands.push('php artisan config:cache');
        commands.push('php artisan route:cache');
        commands.push('php artisan view:cache');
    }
    
    if (options.npm) {
        commands.push('npm install');
        commands.push('npm run build');
    }
    
    if (options.composer) {
        commands.push('composer install --no-dev --optimize-autoloader');
    }
    
    return commands;
}

// Jalankan deployment SSH
function executeSSH(config, commands) {
    const commandString = commands.join(' && ');
    const sshCommand = `ssh ${config.user}@${config.host} "${commandString}"`;
    
    log('\n📋 Perintah yang akan dijalankan:', 'cyan');
    commands.forEach((cmd, i) => {
        console.log(`   ${colors.bright}${i + 1}.${colors.reset} ${cmd}`);
    });
    console.log('');
    
    logStep('SSH', `Menghubungkan ke ${config.user}@${config.host}...`);
    log('(Masukkan password SSH saat diminta)\n', 'yellow');
    
    try {
        // Pakai spawn untuk SSH interaktif (input password)
        const ssh = spawn('ssh', [
            `${config.user}@${config.host}`,
            commandString
        ], {
            stdio: 'inherit',
            shell: true
        });
        
        return new Promise((resolve) => {
            ssh.on('close', (code) => {
                if (code === 0) {
                    logSuccess('Deployment berhasil!');
                    resolve(true);
                } else {
                    logError(`Deployment gagal dengan kode ${code}`);
                    resolve(false);
                }
            });
            
            ssh.on('error', (err) => {
                logError(`Error SSH: ${err.message}`);
                resolve(false);
            });
        });
    } catch (e) {
        logError(`Gagal menghubungkan: ${e.message}`);
        return false;
    }
}

// Copy perintah ke clipboard (Windows)
function copyToClipboard(text) {
    try {
        execSync(`echo ${text} | clip`, { shell: true });
        return true;
    } catch (e) {
        return false;
    }
}

// Alur deployment utama
async function main() {
    const args = process.argv.slice(2);
    const quickMode = args.includes('--quick') || args.includes('-q');
    const copyMode = args.includes('--copy') || args.includes('-c');
    
    console.log('');
    log('╔════════════════════════════════════════════╗', 'cyan');
    log('║     🚀 Shuriza Store - Deploy ke Server    ║', 'cyan');
    log('╚════════════════════════════════════════════╝', 'cyan');
    console.log('');
    
    const config = loadConfig();
    const rl = createRL();
    
    try {
        // Tampilkan status git saat ini
        const currentBranch = getCurrentBranch();
        const lastCommit = getLastCommit();
        const isClean = checkGitStatus();
        
        log('📊 Status Repository Lokal:', 'bright');
        console.log(`   Branch: ${colors.cyan}${currentBranch}${colors.reset}`);
        console.log(`   Commit terakhir: ${colors.green}${lastCommit}${colors.reset}`);
        console.log(`   Working tree: ${isClean ? colors.green + 'Bersih ✓' : colors.yellow + 'Ada perubahan belum di-commit ⚠'}${colors.reset}`);
        console.log('');
        
        if (!isClean && !quickMode) {
            logWarning('Ada perubahan yang belum di-commit!');
            const proceed = await confirm(rl, 'Lanjutkan saja?');
            if (!proceed) {
                log('\nDeployment dibatalkan. Commit dulu perubahanmu.', 'yellow');
                rl.close();
                return;
            }
        }
        
        // Konfigurasi server
        if (!quickMode) {
            log('⚙️  Konfigurasi Server:', 'bright');
            config.host = await question(rl, 'IP/hostname server', config.host);
            config.user = await question(rl, 'Username SSH', config.user);
            config.path = await question(rl, 'Path project di server', config.path);
            config.branch = await question(rl, 'Branch untuk deploy', config.branch);
            
            const saveIt = await confirm(rl, 'Simpan konfigurasi ini untuk lain kali?');
            if (saveIt) {
                saveConfig(config);
            }
            console.log('');
        }
        
        // Opsi deployment
        let options = {
            migrate: true,
            seed: false,
            clearCache: true,
            optimize: false,
            npm: false,
            composer: false
        };
        
        if (!quickMode) {
            log('📦 Opsi Deployment:', 'bright');
            options.migrate = await confirm(rl, 'Jalankan migrasi database?');
            options.seed = await confirm(rl, 'Jalankan seeder database?');
            options.clearCache = await confirm(rl, 'Hapus semua cache?');
            options.optimize = await confirm(rl, 'Optimasi (cache config/routes)?');
            options.npm = await confirm(rl, 'Jalankan npm install & build?');
            options.composer = await confirm(rl, 'Jalankan composer install?');
            console.log('');
        }
        
        // Generate perintah
        const commands = generateDeployCommands(config, options);
        
        // Mode copy - hanya tampilkan perintah
        if (copyMode) {
            const fullCommand = `ssh ${config.user}@${config.host}\n\n${commands.join('\n')}`;
            log('📋 Perintah untuk dijalankan di server:', 'bright');
            console.log('');
            commands.forEach(cmd => console.log(`   ${cmd}`));
            console.log('');
            
            const copyCmd = commands.join(' && ');
            if (copyToClipboard(copyCmd)) {
                logSuccess('Perintah sudah di-copy ke clipboard!');
            }
            
            log(`\n💡 Atau SSH langsung: ssh ${config.user}@${config.host}`, 'cyan');
            rl.close();
            return;
        }
        
        // Push perubahan dulu?
        if (!quickMode && currentBranch !== config.branch) {
            const shouldPush = await confirm(rl, `Push ${currentBranch} ke ${config.branch} sebelum deploy?`);
            if (shouldPush) {
                if (!pushChanges(currentBranch, config.branch)) {
                    const continueAnyway = await confirm(rl, 'Push gagal. Lanjutkan deployment saja?');
                    if (!continueAnyway) {
                        rl.close();
                        return;
                    }
                }
            }
        }
        
        // Konfirmasi akhir
        if (!quickMode) {
            console.log('');
            log('⚠️  Siap deploy ke server:', 'yellow');
            console.log(`   Server: ${colors.cyan}${config.user}@${config.host}${colors.reset}`);
            console.log(`   Path: ${colors.cyan}${config.path}${colors.reset}`);
            console.log(`   Branch: ${colors.cyan}${config.branch}${colors.reset}`);
            console.log('');
            
            const finalConfirm = await confirm(rl, 'Lanjutkan deployment?');
            if (!finalConfirm) {
                log('\nDeployment dibatalkan.', 'yellow');
                rl.close();
                return;
            }
        }
        
        rl.close();
        
        // Jalankan deployment
        console.log('');
        await executeSSH(config, commands);
        
        console.log('');
        log('═══════════════════════════════════════', 'cyan');
        log('  Proses deployment selesai!', 'green');
        log('═══════════════════════════════════════', 'cyan');
        console.log('');
        
    } catch (e) {
        logError(`Error: ${e.message}`);
        rl.close();
    }
}

// Tampilkan bantuan
if (process.argv.includes('--help') || process.argv.includes('-h')) {
    console.log(`
${colors.cyan}Shuriza Store - Deploy Helper${colors.reset}

${colors.bright}Penggunaan:${colors.reset}
  node deploy.cjs           Deploy interaktif (wizard)
  node deploy.cjs --quick   Deploy cepat pakai config tersimpan
  node deploy.cjs --copy    Tampilkan/copy perintah (tanpa SSH)
  npm run deploy            Sama dengan node deploy.cjs

${colors.bright}Opsi:${colors.reset}
  -q, --quick    Lewati konfirmasi, pakai config tersimpan
  -c, --copy     Copy perintah ke clipboard, tidak jalankan SSH
  -h, --help     Tampilkan bantuan ini

${colors.bright}Config Tersimpan:${colors.reset}
  Konfigurasi disimpan di .deploy-config.json
  (File ini sudah di-gitignore untuk keamanan)
`);
    process.exit(0);
}

main();
