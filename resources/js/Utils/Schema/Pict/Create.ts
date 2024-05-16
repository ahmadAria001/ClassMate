import { z } from 'zod'

const MAX_FILE_SIZE = 2097152;
const ACCEPTED_IMAGE_TYPES: Array<String> = ["image/jpeg", "image/jpg", "image/png"];


export const createSchema = z.object({
    img: z.any()
        .refine((file) => file?.size <= MAX_FILE_SIZE, `File harus kurang dari 2MB.`)
        .refine(
            (file) => ACCEPTED_IMAGE_TYPES.includes(file?.type),
            "Hanya format .jpg, .jpeg, dan .png yang dapat digunakan."
        )
})

export type CreateSchema = z.infer<typeof createSchema>;


