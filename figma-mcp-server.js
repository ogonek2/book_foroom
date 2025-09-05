#!/usr/bin/env node

const express = require('express');
const cors = require('cors');
const axios = require('axios');

const app = express();
const PORT = 3845;

// Middleware
app.use(cors());
app.use(express.json());

// Figma API configuration
const FIGMA_API_BASE = 'https://api.figma.com/v1';
const FIGMA_TOKEN = '';

// Helper function to make Figma API requests
async function makeFigmaRequest(endpoint, params = {}) {
    try {
        const response = await axios.get(`${FIGMA_API_BASE}${endpoint}`, {
            headers: {
                'Authorization': `Bearer ${FIGMA_TOKEN}`,
                'Content-Type': 'application/json'
            },
            params
        });
        return response.data;
    } catch (error) {
        console.error('Figma API Error:', error.response?.data || error.message);
        throw error;
    }
}

// MCP Protocol endpoints
app.post('/mcp', async (req, res) => {
    try {
        const { method, params } = req.body;
        
        let result;
        
        switch (method) {
            case 'get_document_info':
                result = await makeFigmaRequest(`/files/${params.fileKey}`);
                break;
                
            case 'get_file_images':
                result = await makeFigmaRequest(`/images/${params.fileKey}`, {
                    ids: params.nodeIds?.join(',') || '',
                    format: params.format || 'png',
                    scale: params.scale || 1
                });
                break;
                
            case 'get_comments':
                result = await makeFigmaRequest(`/files/${params.fileKey}/comments`);
                break;
                
            case 'get_components':
                result = await makeFigmaRequest(`/files/${params.fileKey}/components`);
                break;
                
            case 'get_styles':
                result = await makeFigmaRequest(`/files/${params.fileKey}/styles`);
                break;
                
            case 'get_team_projects':
                result = await makeFigmaRequest(`/teams/${params.teamId}/projects`);
                break;
                
            case 'get_project_files':
                result = await makeFigmaRequest(`/projects/${params.projectId}/files`);
                break;
                
            case 'get_file_nodes':
                result = await makeFigmaRequest(`/files/${params.fileKey}/nodes`, {
                    ids: params.nodeIds?.join(',') || ''
                });
                break;
                
            case 'get_team_components':
                result = await makeFigmaRequest(`/teams/${params.teamId}/components`);
                break;
                
            case 'get_team_styles':
                result = await makeFigmaRequest(`/teams/${params.teamId}/styles`);
                break;
                
            case 'get_user_me':
                result = await makeFigmaRequest('/me');
                break;
                
            case 'get_team_info':
                result = await makeFigmaRequest(`/teams/${params.teamId}`);
                break;
                
            case 'get_project_info':
                result = await makeFigmaRequest(`/projects/${params.projectId}`);
                break;
                
            case 'get_file_versions':
                result = await makeFigmaRequest(`/files/${params.fileKey}/versions`);
                break;
                
            case 'get_file_metadata':
                result = await makeFigmaRequest(`/files/${params.fileKey}/metadata`);
                break;
                
            case 'get_team_projects_paginated':
                result = await makeFigmaRequest(`/teams/${params.teamId}/projects`, {
                    page_size: params.pageSize || 30,
                    cursor: params.cursor || ''
                });
                break;
                
            case 'get_project_files_paginated':
                result = await makeFigmaRequest(`/projects/${params.projectId}/files`, {
                    page_size: params.pageSize || 30,
                    cursor: params.cursor || ''
                });
                break;
                
            case 'get_team_components_paginated':
                result = await makeFigmaRequest(`/teams/${params.teamId}/components`, {
                    page_size: params.pageSize || 30,
                    cursor: params.cursor || ''
                });
                break;
                
            case 'get_team_styles_paginated':
                result = await makeFigmaRequest(`/teams/${params.teamId}/styles`, {
                    page_size: params.pageSize || 30,
                    cursor: params.cursor || ''
                });
                break;
                
            case 'get_file_comments_paginated':
                result = await makeFigmaRequest(`/files/${params.fileKey}/comments`, {
                    page_size: params.pageSize || 30,
                    cursor: params.cursor || ''
                });
                break;
                
            case 'get_file_versions_paginated':
                result = await makeFigmaRequest(`/files/${params.fileKey}/versions`, {
                    page_size: params.pageSize || 30,
                    cursor: params.cursor || ''
                });
                break;
                
            case 'get_file_metadata_paginated':
                result = await makeFigmaRequest(`/files/${params.fileKey}/metadata`, {
                    page_size: params.pageSize || 30,
                    cursor: params.cursor || ''
                });
                break;
                
            default:
                return res.status(400).json({
                    error: 'Unknown method',
                    message: `Method '${method}' is not supported`
                });
        }
        
        res.json({
            success: true,
            data: result
        });
        
    } catch (error) {
        console.error('MCP Server Error:', error);
        res.status(500).json({
            success: false,
            error: error.message,
            data: error.response?.data || null
        });
    }
});

// Health check endpoint
app.get('/health', (req, res) => {
    res.json({ 
        status: 'ok', 
        message: 'Figma MCP Server is running',
        port: PORT,
        timestamp: new Date().toISOString()
    });
});

// Start server
app.listen(PORT, '127.0.0.1', () => {
    console.log(`🚀 Figma MCP Server running on http://127.0.0.1:${PORT}`);
    console.log(`📋 Health check: http://127.0.0.1:${PORT}/health`);
    console.log(`🔗 MCP endpoint: http://127.0.0.1:${PORT}/mcp`);
});

// Graceful shutdown
process.on('SIGINT', () => {
    console.log('\n🛑 Shutting down Figma MCP Server...');
    process.exit(0);
});

process.on('SIGTERM', () => {
    console.log('\n🛑 Shutting down Figma MCP Server...');
    process.exit(0);
});
