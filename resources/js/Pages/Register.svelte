<script lang="ts">
    import { Label, Input, Checkbox, A, Button } from "flowbite-svelte";
    export let site = {
        name: "KawanDesa",
        img: "assets/icons/KD_logo.svg",
        link: "/",
        imgAlt: "KawanDesa Logo",
    };
</script>

<main class="w-full">
    <div
        class="flex flex-col items-center justify-center px-6 pt-8 max-auto md:h-screen pt:mt-0"
    >
        <div class="md:w-1/3">
            <a
                href={site.link}
                class="flex flex-col items-center justify-center mb-3"
            >
                <img src={site.img} alt={site.imgAlt} class="mb-3" />
                <span class="text-2xl font-bold">{site.name}</span>
            </a>
            <form action="" method="POST">
                <Label class="space-y-2 mb-3">
                    <span>NIK</span>
                    <Input
                        type="text"
                        name="nik"
                        placeholder="Masukan NIK anda"
                        required
                    />
                </Label>
                <Label class="space-y-2 mb-3">
                    <span>Password</span>
                    <Input
                        type="password"
                        name="password"
                        placeholder="Masukan password anda"
                        required
                    />
                </Label>
                <Label class="space-y-2 mb-3">
                    <span>Konfirmasi Password</span>
                    <Input
                        type="password"
                        name="password-confirmation"
                        placeholder="Konfirmasi password anda"
                        required
                    />
                </Label>
                <div class="flex items-start mb-3">
                    <Checkbox color="blue"
                        >Saya menerima syarat dan ketentuan yang berlaku</Checkbox
                    >
                    <a
                        href="/"
                        class="ml-auto text-sm text-blue-700 hover:underline dark:text-blue-500"
                        >Lupa Password</a
                    >
                </div>
                <!-- Sementara link kearahkan ke login karena belum bisa untuk logika backend -->
                <a href="/login">
                    <Button type="submit" class="w-full" color="blue"
                        >Daftar</Button
                    >
                </a>
                <Label class="font-bold mt-3"
                    >Sudah Punya Akun? <A href="/">Login</A></Label
                >
            </form>
        </div>
    </div>
</main>
