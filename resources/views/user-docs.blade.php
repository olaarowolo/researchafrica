<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Research Africa App Documentation</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- React & ReactDOM -->
    <script crossorigin src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
    <script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>

    <!-- Babel for JSX -->
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>

    <!-- Custom Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Smooth scrolling for anchor links */
        html {
            scroll-behavior: smooth;
        }

        /* Hide scrollbar for sidebar but keep functionality */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900">

    <div id="root"></div>

    <script type="text/babel">
        const { useState, useEffect } = React;

        // --- Icons (Implemented manually to avoid external dependencies) ---
        const Icons = {
            Book: (props) => <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" {...props}><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>,
            Users: (props) => <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" {...props}><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>,
            Shield: (props) => <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" {...props}><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>,
            PenTool: (props) => <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" {...props}><path d="m12 19 7-7 3 3-7 7-3-3z"/><path d="m18 13-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/><path d="m2 2 7.586 7.586"/><circle cx="11" cy="11" r="2"/></svg>,
            FileText: (props) => <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" {...props}><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>,
            Send: (props) => <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" {...props}><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>,
            Layers: (props) => <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" {...props}><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>,
            Printer: (props) => <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" {...props}><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>,
            Menu: (props) => <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" {...props}><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="18" x2="20" y2="18"/></svg>,
            X: (props) => <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" {...props}><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>,
            ChevronRight: (props) => <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" {...props}><polyline points="9 18 15 12 9 6"/></svg>,
            Search: (props) => <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" {...props}><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>,
            CheckCircle2: (props) => <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" {...props}><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>,
            HelpCircle: (props) => <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" {...props}><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>,
            LayoutDashboard: (props) => <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" {...props}><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>,
            GitPullRequest: (props) => <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" {...props}><circle cx="18" cy="18" r="3"/><circle cx="6" cy="6" r="3"/><path d="M13 6h3a2 2 0 0 1 2 2v7"/><line x1="6" y1="9" x2="6" y2="21"/></svg>
        };

        // --- Data Structure ---

        const navItems = [
            { id: 'overview', label: 'Overview', icon: Icons.LayoutDashboard },
            { id: 'super-admin', label: 'Super Admin', icon: Icons.Shield },
            { id: 'journal-admin', label: 'Journal Admin', icon: Icons.Layers },
            { id: 'editor', label: 'Editor', icon: Icons.PenTool },
            { id: 'reviewer', label: 'Reviewer', icon: Icons.FileText },
            { id: 'author', label: 'Author', icon: Icons.Send },
            { id: 'contributor', label: 'Contributor', icon: Icons.Users },
            { id: 'publisher', label: 'Publisher', icon: Icons.Printer },
        ];

        const workflows = {
            'super-admin': {
                title: 'Platform Super Admin',
                description: 'The Super Admin manages the entire platform, onboarding new journals, configuring global settings, assigning Journal Admins, managing all users, and monitoring analytics.',
                steps: [
                    { title: 'Onboard Journal', desc: 'Initialize new journal instances on the platform.' },
                    { title: 'Configure Global Settings', desc: 'Set platform-wide parameters and defaults.' },
                    { title: 'Assign Journal Admins', desc: 'Delegate control to specific journal managers.' },
                    { title: 'Manage Users/Roles', desc: 'Oversee the global user database and permission sets.' },
                    { title: 'Monitor & Support', desc: 'View platform analytics and troubleshoot issues.' }
                ]
            },
            'journal-admin': {
                title: 'Journal Admin',
                description: 'The Journal Admin sets up their journal, adds and manages users, assigns roles, oversees all submissions and peer review, and monitors journal-specific analytics.',
                steps: [
                    { title: 'Configure Journal', desc: 'Set up submission guidelines, sections, and emails.' },
                    { title: 'User Management', desc: 'Add users and assign specific roles (Editor, Reviewer, etc).' },
                    { title: 'Oversee Submissions', desc: 'Monitor the flow of manuscripts through the pipeline.' },
                    { title: 'Monitor Analytics', desc: 'Track journal performance and submission stats.' }
                ]
            },
            'editor': {
                title: 'Editor',
                description: 'Editors manage incoming submissions, assign reviewers, track review progress, make editorial decisions (accept, revise, reject), and communicate with authors and reviewers.',
                steps: [
                    { title: 'Manage Submissions', desc: 'Screen new manuscripts for quality and fit.' },
                    { title: 'Assign Reviewers', desc: 'Select and invite qualified peers for blind review.' },
                    { title: 'Track Reviews', desc: 'Monitor reviewer deadlines and response quality.' },
                    { title: 'Editorial Decision', desc: 'Decide: Accept, Revisions Required, or Reject.' },
                    { title: 'Communication', desc: 'Send decision letters and feedback to authors.' }
                ]
            },
            'reviewer': {
                title: 'Reviewer',
                description: 'Reviewers access assigned manuscripts, download files, submit structured reviews, and can track their review history and deadlines.',
                steps: [
                    { title: 'Receive Assignment', desc: 'View assigned manuscripts in dashboard.' },
                    { title: 'Review Materials', desc: 'Download manuscript and supplementary files.' },
                    { title: 'Submit Review', desc: 'Fill out evaluation forms and provide comments.' },
                    { title: 'History', desc: 'Track completed reviews and recognition.' }
                ]
            },
            'author': {
                title: 'Author',
                description: 'Authors submit manuscripts, track their status, respond to editorial and reviewer feedback, revise and resubmit as needed, and view final decisions and publication status.',
                steps: [
                    { title: 'Submit Manuscript', desc: 'Upload metadata, files, and conflict of interest forms.' },
                    { title: 'Track Status', desc: 'Monitor progress (e.g., "In Review", "Awaiting Decision").' },
                    { title: 'Respond to Feedback', desc: 'Address reviewer comments if revisions are requested.' },
                    { title: 'Revise & Resubmit', desc: 'Upload corrected versions of the manuscript.' },
                    { title: 'Publication', desc: 'View final decision and published article.' }
                ]
            },
            'contributor': {
                title: 'Contributor',
                description: 'Contributors (such as guest editors or section editors) have custom permissions and perform editorial or review tasks as assigned by the Journal Admin.',
                steps: [
                    { title: 'Access Journal', desc: 'Log in with assigned permissions.' },
                    { title: 'Execute Tasks', desc: 'Perform specific editorial or review duties as delegated.' }
                ]
            },
            'publisher': {
                title: 'Publisher (Galley Proof Workflow)',
                description: 'The Publisher manages the final production phase, including Galley Proofs and final PDF generation.',
                steps: [
                    { title: 'Access Ready Articles', desc: 'Identify articles accepted for publication.' },
                    { title: 'Upload Galley Proof', desc: 'Upload the formatted PDF proof.' },
                    { title: 'System Notification', desc: 'System automatically notifies the Corresponding Author.', type: 'system' },
                    { title: 'Author Approval', desc: 'Author approves or rejects the proof.', type: 'interaction' },
                    { title: 'Final Version', desc: 'If approved, Publisher uploads final clean PDF.' },
                    { title: 'Publish', desc: 'Schedule and release the article to the public.' }
                ]
            }
        };

        // --- Components ---

        const Sidebar = ({ activeSection, setActiveSection, isMobileOpen, setIsMobileOpen }) => {
            return (
                <React.Fragment>
                    {/* Mobile Overlay */}
                    {isMobileOpen && (
                        <div
                            className="fixed inset-0 bg-black/50 z-40 lg:hidden"
                            onClick={() => setIsMobileOpen(false)}
                        />
                    )}

                    {/* Sidebar Container */}
                    <aside className={`
                        fixed top-0 left-0 z-50 h-full w-72 bg-slate-900 text-slate-300 transition-transform duration-300 ease-in-out flex flex-col
                        ${isMobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'}
                    `}>
                        {/* Logo Area */}
                        <div className="p-6 border-b border-slate-800 flex items-center gap-3">
                            <div className="bg-indigo-500 p-2 rounded-lg">
                                <Icons.Book className="text-white w-6 h-6" />
                            </div>
                            <div className="font-bold text-white text-lg tracking-tight">
                                Research<span className="text-indigo-400">Africa</span>
                            </div>
                        </div>

                        {/* Navigation */}
                        <nav className="flex-1 overflow-y-auto py-6 px-4 space-y-1 no-scrollbar">
                            {navItems.map((item) => (
                                <button
                                    key={item.id}
                                    onClick={() => {
                                        setActiveSection(item.id);
                                        setIsMobileOpen(false);
                                    }}
                                    className={`
                                        w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-all
                                        ${activeSection === item.id
                                            ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/50'
                                            : 'hover:bg-slate-800 hover:text-white'}
                                    `}
                                >
                                    <item.icon className={`w-5 h-5 ${activeSection === item.id ? 'text-indigo-200' : 'text-slate-500'}`} />
                                    {item.label}
                                </button>
                            ))}
                        </nav>

                        {/* Footer */}
                        <div className="p-4 border-t border-slate-800 text-xs text-slate-500">
                            <p>© 2024 Research Africa</p>
                            <p className="mt-1">v2.4.0 Documentation</p>
                        </div>
                    </aside>
                </React.Fragment>
            );
        };

        const WorkflowTimeline = ({ steps }) => {
            return (
                <div className="relative mt-8 ml-4 mb-8">
                    {/* Vertical Line */}
                    <div className="absolute left-[15px] top-4 bottom-4 w-0.5 bg-slate-200"></div>

                    {steps.map((step, index) => (
                        <div key={index} className="relative flex items-start mb-8 last:mb-0 group">
                            {/* Node */}
                            <div className={`
                                relative z-10 flex items-center justify-center w-8 h-8 rounded-full border-4 shadow-sm transition-colors
                                ${step.type === 'system'
                                    ? 'bg-amber-100 border-white text-amber-600'
                                    : step.type === 'interaction'
                                        ? 'bg-purple-100 border-white text-purple-600'
                                        : 'bg-indigo-50 border-white text-indigo-600'}
                            `}>
                                {step.type === 'system' ? (
                                    <div className="w-2.5 h-2.5 rounded-full bg-amber-500"></div>
                                ) : step.type === 'interaction' ? (
                                    <Icons.GitPullRequest className="w-4 h-4" />
                                ) : (
                                    <div className="w-2.5 h-2.5 rounded-full bg-indigo-600"></div>
                                )}
                            </div>

                            {/* Content */}
                            <div className="ml-6 flex-1 pt-1">
                                <h4 className="text-sm font-bold text-slate-900 flex items-center gap-2">
                                    {step.title}
                                    {step.type === 'system' && <span className="px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[10px] uppercase tracking-wide">Automated</span>}
                                    {step.type === 'interaction' && <span className="px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 text-[10px] uppercase tracking-wide">Interaction</span>}
                                </h4>
                                <p className="text-slate-600 text-sm mt-1 leading-relaxed">
                                    {step.desc}
                                </p>
                            </div>
                        </div>
                    ))}
                </div>
            );
        };

        const ContentCard = ({ id, title, description, steps, icon: Icon }) => (
            <div id={id} className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-12 scroll-mt-24">
                <div className="p-8 border-b border-slate-100 bg-slate-50/50">
                    <div className="flex items-center gap-4 mb-4">
                        <div className="p-3 bg-white rounded-xl shadow-sm border border-slate-100 text-indigo-600">
                            <Icon className="w-8 h-8" />
                        </div>
                        <h2 className="text-2xl font-bold text-slate-900">{title}</h2>
                    </div>
                    <p className="text-slate-600 leading-relaxed text-lg max-w-3xl">
                        {description}
                    </p>
                </div>

                <div className="p-8 bg-white">
                    <h3 className="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-6">Workflow Process</h3>
                    <WorkflowTimeline steps={steps} />
                </div>
            </div>
        );

        const OverviewSection = ({setActiveSection}) => (
            <div className="space-y-8 animate-in fade-in duration-500">
                <div className="bg-indigo-600 rounded-3xl p-8 sm:p-12 text-white relative overflow-hidden shadow-lg">
                    <div className="relative z-10 max-w-2xl">
                        <h1 className="text-3xl sm:text-4xl font-bold mb-4">Research Africa Documentation</h1>
                        <p className="text-indigo-100 text-lg leading-relaxed mb-8">
                            Welcome to the comprehensive user guide. Learn how to manage journals, submit manuscripts, and navigate the peer review process efficiently.
                        </p>
                        <div className="flex flex-wrap gap-4">
                            <button
                                onClick={() => setActiveSection('author')}
                                className="bg-white text-indigo-600 px-6 py-3 rounded-lg font-semibold hover:bg-indigo-50 transition-colors flex items-center gap-2"
                            >
                                Get Started <Icons.ChevronRight className="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                    {/* Decorative background element */}
                    <div className="absolute -right-10 -bottom-20 opacity-10 pointer-events-none">
                        <Icons.Book width={320} height={320} />
                    </div>
                </div>

                <div className="grid md:grid-cols-2 gap-6">
                    <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                        <h3 className="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                            <div className="w-1 h-6 bg-green-500 rounded-full"></div>
                            How the App Works
                        </h3>
                        <ul className="space-y-4">
                            {[
                                { bold: 'Journals', text: 'Onboarded by Super Admins, managed by Journal Admins.' },
                                { bold: 'Users', text: 'Assigned distinct roles (Editor, Reviewer, etc.) with scoped permissions.' },
                                { bold: 'Manuscripts', text: 'Follow a linear path: Submission → Review → Decision → Publication.' },
                                { bold: 'Tracking', text: 'All actions are logged for transparency and audit trails.' },
                            ].map((item, idx) => (
                                <li key={idx} className="flex gap-3 text-slate-600 text-sm">
                                    <Icons.CheckCircle2 className="w-5 h-5 text-green-500 flex-shrink-0" />
                                    <span><span className="font-semibold text-slate-900">{item.bold}</span> {item.text}</span>
                                </li>
                            ))}
                        </ul>
                    </div>

                    <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                        <h3 className="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                            <div className="w-1 h-6 bg-blue-500 rounded-full"></div>
                            Quick Start Guide
                        </h3>
                        <ol className="space-y-4 relative ml-2">
                            <div className="absolute left-[11px] top-2 bottom-2 w-px bg-slate-100"></div>
                            {[
                                { title: 'Register', text: 'Sign up or accept an invitation email.' },
                                { title: 'Log In', text: 'Access your dedicated dashboard.' },
                                { title: 'Action', text: 'Submit, review, or edit based on your role.' },
                                { title: 'Notifications', text: 'Check the bell icon for updates.' },
                            ].map((item, idx) => (
                                <li key={idx} className="relative flex gap-4 text-sm">
                                    <span className="relative z-10 flex items-center justify-center w-6 h-6 rounded-full bg-slate-100 text-slate-600 font-bold text-xs border-2 border-white">
                                        {idx + 1}
                                    </span>
                                    <div>
                                        <span className="font-semibold text-slate-900 block">{item.title}</span>
                                        <span className="text-slate-500">{item.text}</span>
                                    </div>
                                </li>
                            ))}
                        </ol>
                    </div>
                </div>
            </div>
        );

        // --- Main Application ---

        const App = () => {
            const [activeSection, setActiveSection] = useState('overview');
            const [isMobileOpen, setIsMobileOpen] = useState(false);

            useEffect(() => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }, [activeSection]);

            const renderContent = () => {
                if (activeSection === 'overview') {
                    return <OverviewSection setActiveSection={setActiveSection} />;
                }

                const workflowData = workflows[activeSection];
                const navItem = navItems.find(n => n.id === activeSection);

                if (workflowData) {
                    return (
                        <div className="animate-in slide-in-from-bottom-4 duration-500 fade-in">
                            <ContentCard
                                id={activeSection}
                                {...workflowData}
                                icon={navItem?.icon || Icons.FileText}
                            />
                        </div>
                    );
                }
                return <div>Content not found</div>;
            };

            return (
                <div className="min-h-screen bg-slate-50 font-sans text-slate-900 selection:bg-indigo-100 selection:text-indigo-800 flex flex-col lg:flex-row">

                    <Sidebar
                        activeSection={activeSection}
                        setActiveSection={setActiveSection}
                        isMobileOpen={isMobileOpen}
                        setIsMobileOpen={setIsMobileOpen}
                    />

                    {/* Main Content Wrapper */}
                    <main className="lg:ml-72 min-h-screen flex flex-col flex-1 transition-all duration-300 w-full">

                        {/* Mobile Header */}
                        <header className="bg-white border-b border-slate-200 sticky top-0 z-30 lg:hidden px-4 py-3 flex items-center justify-between shadow-sm">
                            <div className="flex items-center gap-3">
                                <button onClick={() => setIsMobileOpen(true)} className="p-2 -ml-2 text-slate-600 hover:bg-slate-100 rounded-lg">
                                    <Icons.Menu className="w-6 h-6" />
                                </button>
                                <span className="font-bold text-slate-900">Research Africa</span>
                            </div>
                        </header>

                        {/* Desktop Header / Utility Bar */}
                        <header className="hidden lg:flex sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-slate-200 px-8 py-4 justify-between items-center">
                            <div className="flex items-center text-slate-400 gap-2 bg-slate-100 px-3 py-1.5 rounded-lg text-sm w-64 border border-transparent focus-within:border-indigo-500 focus-within:bg-white transition-all cursor-text">
                                <Icons.Search className="w-4 h-4" />
                                <span>Search documentation...</span>
                            </div>
                            <div className="flex items-center gap-4">
                                <button className="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors">Support</button>
                                <button className="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors">API</button>
                                <div className="w-px h-4 bg-slate-300"></div>
                                <div className="flex items-center gap-2">
                                    <span className="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                                    <span className="text-xs font-medium text-slate-500">System Operational</span>
                                </div>
                            </div>
                        </header>

                        {/* Content Area */}
                        <div className="flex-1 p-4 sm:p-8 lg:p-12 max-w-6xl mx-auto w-full">
                            {renderContent()}
                        </div>

                        {/* Footer */}
                        <footer className="mt-auto border-t border-slate-200 bg-white px-4 lg:px-8 py-12">
                            <div className="max-w-6xl mx-auto flex flex-col md:flex-row justify-between items-center gap-6">
                                <div className="text-sm text-slate-500 text-center md:text-left">
                                    Need technical assistance? <a href="#" className="text-indigo-600 font-medium hover:underline">Contact the Development Team</a>
                                </div>
                                <div className="flex items-center gap-6">
                                    <a href="#" className="text-slate-400 hover:text-slate-600 transition-colors"><Icons.HelpCircle className="w-5 h-5"/></a>
                                </div>
                            </div>
                        </footer>

                    </main>
                </div>
            );
        };

        const root = ReactDOM.createRoot(document.getElementById('root'));
        root.render(<App />);
    </script>
</body>

</html>
