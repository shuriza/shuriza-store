#!/usr/bin/env node
/**
 * Shuriza Store - Deployment Helper Script
 * 
 * Usage:
 *   node deploy.js           - Interactive deployment
 *   node deploy.js --quick   - Quick deploy (skip confirmations)
 *   npm run deploy           - Same as node deploy.js
 */

const { execSync, spawn } = require('child_process');
const readline = require('readline');
const fs = require('fs');
const path = require('path');

// Configuration
const CONFIG_FILE = path.join(__dirname, '.deploy-config.json');
const DEFAULT_CONFIG = {
    host: '68.183.182.39',
    user: 'root',
    path: '/var/www/shuriza-store',
    branch: 'main'
};

// Colors for terminal output
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

// Load or create config
function loadConfig() {
    try {
        if (fs.existsSync(CONFIG_FILE)) {
            const saved = JSON.parse(fs.readFileSync(CONFIG_FILE, 'utf8'));
            return { ...DEFAULT_CONFIG, ...saved };
        }
    } catch (e) {
        logWarning('Could not load config, using defaults');
    }
    return DEFAULT_CONFIG;
}

function saveConfig(config) {
    try {
        fs.writeFileSync(CONFIG_FILE, JSON.stringify(config, null, 2));
        logSuccess('Configuration saved');
    } catch (e) {
        logWarning('Could not save config');
    }
}

// Create readline interface
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

// Check local git status
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
        return 'unknown';
    }
}

function getLastCommit() {
    try {
        return execSync('git log -1 --pretty=format:"%h - %s"', { encoding: 'utf8' }).trim();
    } catch (e) {
        return 'unknown';
    }
}

// Push local changes
function pushChanges(branch, targetBranch) {
    logStep('PUSH', `Pushing ${branch} to ${targetBranch}...`);
    try {
        execSync(`git push origin ${branch}:${targetBranch}`, { stdio: 'inherit' });
        logSuccess('Changes pushed successfully');
        return true;
    } catch (e) {
        logError('Failed to push changes');
        return false;
    }
}

// Generate SSH commands
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

// Execute SSH deployment
function executeSSH(config, commands) {
    const commandString = commands.join(' && ');
    const sshCommand = `ssh ${config.user}@${config.host} "${commandString}"`;
    
    log('\n📋 Commands to execute:', 'cyan');
    commands.forEach((cmd, i) => {
        console.log(`   ${colors.bright}${i + 1}.${colors.reset} ${cmd}`);
    });
    console.log('');
    
    logStep('SSH', `Connecting to ${config.user}@${config.host}...`);
    log('(Enter your SSH password when prompted)\n', 'yellow');
    
    try {
        // Use spawn for interactive SSH (password input)
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
                    logSuccess('Deployment completed successfully!');
                    resolve(true);
                } else {
                    logError(`Deployment failed with code ${code}`);
                    resolve(false);
                }
            });
            
            ssh.on('error', (err) => {
                logError(`SSH error: ${err.message}`);
                resolve(false);
            });
        });
    } catch (e) {
        logError(`Failed to connect: ${e.message}`);
        return false;
    }
}

// Copy command to clipboard (Windows)
function copyToClipboard(text) {
    try {
        execSync(`echo ${text} | clip`, { shell: true });
        return true;
    } catch (e) {
        return false;
    }
}

// Main deployment flow
async function main() {
    const args = process.argv.slice(2);
    const quickMode = args.includes('--quick') || args.includes('-q');
    const copyMode = args.includes('--copy') || args.includes('-c');
    
    console.log('');
    log('╔════════════════════════════════════════════╗', 'cyan');
    log('║     🚀 Shuriza Store Deployment Helper     ║', 'cyan');
    log('╚════════════════════════════════════════════╝', 'cyan');
    console.log('');
    
    const config = loadConfig();
    const rl = createRL();
    
    try {
        // Show current git status
        const currentBranch = getCurrentBranch();
        const lastCommit = getLastCommit();
        const isClean = checkGitStatus();
        
        log('📊 Local Repository Status:', 'bright');
        console.log(`   Branch: ${colors.cyan}${currentBranch}${colors.reset}`);
        console.log(`   Last commit: ${colors.green}${lastCommit}${colors.reset}`);
        console.log(`   Working tree: ${isClean ? colors.green + 'Clean ✓' : colors.yellow + 'Has uncommitted changes ⚠'}${colors.reset}`);
        console.log('');
        
        if (!isClean && !quickMode) {
            logWarning('You have uncommitted changes!');
            const proceed = await confirm(rl, 'Continue anyway?');
            if (!proceed) {
                log('\nDeployment cancelled. Commit your changes first.', 'yellow');
                rl.close();
                return;
            }
        }
        
        // Configure server
        if (!quickMode) {
            log('⚙️  Server Configuration:', 'bright');
            config.host = await question(rl, 'Server IP/hostname', config.host);
            config.user = await question(rl, 'SSH username', config.user);
            config.path = await question(rl, 'Project path on server', config.path);
            config.branch = await question(rl, 'Branch to deploy', config.branch);
            
            const saveIt = await confirm(rl, 'Save this configuration for next time?');
            if (saveIt) {
                saveConfig(config);
            }
            console.log('');
        }
        
        // Deployment options
        let options = {
            migrate: true,
            seed: false,
            clearCache: true,
            optimize: false,
            npm: false,
            composer: false
        };
        
        if (!quickMode) {
            log('📦 Deployment Options:', 'bright');
            options.migrate = await confirm(rl, 'Run database migrations?');
            options.seed = await confirm(rl, 'Run database seeders?');
            options.clearCache = await confirm(rl, 'Clear all caches?');
            options.optimize = await confirm(rl, 'Optimize (cache config/routes)?');
            options.npm = await confirm(rl, 'Run npm install & build?');
            options.composer = await confirm(rl, 'Run composer install?');
            console.log('');
        }
        
        // Generate commands
        const commands = generateDeployCommands(config, options);
        
        // Copy mode - just show commands
        if (copyMode) {
            const fullCommand = `ssh ${config.user}@${config.host}\n\n${commands.join('\n')}`;
            log('📋 Commands to run on server:', 'bright');
            console.log('');
            commands.forEach(cmd => console.log(`   ${cmd}`));
            console.log('');
            
            const copyCmd = commands.join(' && ');
            if (copyToClipboard(copyCmd)) {
                logSuccess('Commands copied to clipboard!');
            }
            
            log(`\n💡 Or SSH directly: ssh ${config.user}@${config.host}`, 'cyan');
            rl.close();
            return;
        }
        
        // Push changes first?
        if (!quickMode && currentBranch !== config.branch) {
            const shouldPush = await confirm(rl, `Push ${currentBranch} to ${config.branch} before deploying?`);
            if (shouldPush) {
                if (!pushChanges(currentBranch, config.branch)) {
                    const continueAnyway = await confirm(rl, 'Push failed. Continue with deployment anyway?');
                    if (!continueAnyway) {
                        rl.close();
                        return;
                    }
                }
            }
        }
        
        // Final confirmation
        if (!quickMode) {
            console.log('');
            log('⚠️  Ready to deploy to server:', 'yellow');
            console.log(`   Server: ${colors.cyan}${config.user}@${config.host}${colors.reset}`);
            console.log(`   Path: ${colors.cyan}${config.path}${colors.reset}`);
            console.log(`   Branch: ${colors.cyan}${config.branch}${colors.reset}`);
            console.log('');
            
            const finalConfirm = await confirm(rl, 'Proceed with deployment?');
            if (!finalConfirm) {
                log('\nDeployment cancelled.', 'yellow');
                rl.close();
                return;
            }
        }
        
        rl.close();
        
        // Execute deployment
        console.log('');
        await executeSSH(config, commands);
        
        console.log('');
        log('═══════════════════════════════════════', 'cyan');
        log('  Deployment process finished!', 'green');
        log('═══════════════════════════════════════', 'cyan');
        console.log('');
        
    } catch (e) {
        logError(`Error: ${e.message}`);
        rl.close();
    }
}

// Show help
if (process.argv.includes('--help') || process.argv.includes('-h')) {
    console.log(`
${colors.cyan}Shuriza Store Deployment Helper${colors.reset}

${colors.bright}Usage:${colors.reset}
  node deploy.js           Interactive deployment wizard
  node deploy.js --quick   Quick deploy with saved config
  node deploy.js --copy    Just show/copy commands (no SSH)
  npm run deploy           Same as node deploy.js

${colors.bright}Options:${colors.reset}
  -q, --quick    Skip confirmations, use saved config
  -c, --copy     Copy commands to clipboard instead of running
  -h, --help     Show this help message

${colors.bright}Saved Config:${colors.reset}
  Configuration is saved to .deploy-config.json
  (This file is gitignored for security)
`);
    process.exit(0);
}

main();
