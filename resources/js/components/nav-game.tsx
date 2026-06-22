import { Link, usePage } from '@inertiajs/react'
import { Factory, Package, ShoppingBag, Truck } from 'lucide-react'
import GameController from '@/actions/App/Http/Controllers/GameController'
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar'

const TABS = [
    { key: 'resources', label: 'Resources', icon: Package },
    { key: 'factory', label: 'Factory', icon: Factory },
    { key: 'transport', label: 'Transport', icon: Truck },
    { key: 'showrooms', label: 'Showrooms', icon: ShoppingBag },
] as const

export function NavGame() {
    const { url } = usePage()
    const origin = typeof window !== 'undefined' ? window.location.origin : 'http://localhost'
    const parsed = new URL(url, origin)
    const isOnGame = parsed.pathname === '/game'
    const currentTab = parsed.searchParams.get('tab') ?? 'resources'

    return (
        <SidebarGroup className="px-2 py-0">
            <SidebarGroupLabel>Car Empire</SidebarGroupLabel>
            <SidebarMenu>
                {TABS.map(({ key, label, icon: Icon }) => (
                    <SidebarMenuItem key={key}>
                        <SidebarMenuButton
                            asChild
                            isActive={isOnGame && currentTab === key}
                            tooltip={{ children: label }}
                        >
                            <Link href={`${GameController().url}?tab=${key}`}>
                                <Icon />
                                <span>{label}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                ))}
            </SidebarMenu>
        </SidebarGroup>
    )
}
